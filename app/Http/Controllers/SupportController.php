<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Traits\Notify;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportController extends Controller
{
	use Upload, Notify;

	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function index()
	{
		$tickets = Ticket::where('user_id', Auth::id())->latest()->paginate(config('basic.paginate'));
		return view($this->theme . 'user.support.index', compact('tickets'));
	}

	public function create()
	{
		return view($this->theme . 'user.support.create');
	}

	public function store(Request $request)
	{
		$this->newTicketValidation($request);
		$random = rand(100000, 999999);
		$ticket = $this->saveTicket($request, $random);
		$message = $this->saveMsgTicket($request, $ticket);

		$path = config('location.ticket.path');
		if ($request->hasFile('attachments')) {
			foreach ($request->file('attachments') as $image) {
				try {
					$this->saveAttachment($message, $image, $path);
				} catch (\Exception $exp) {
					return back()->withInput()->with('alert', 'Could not upload your ' . $image);
				}
			}
		}

		$msg = [
			'username' => optional($ticket->user)->username,
			'ticket_id' => $ticket->ticket
		];
		$action = [
			"link" => route('admin.ticket.view', $ticket->id),
			"icon" => "fas fa-ticket-alt text-white"
		];

		$this->adminPushNotification('SUPPORT_TICKET_CREATE', $msg, $action);
		$firebaseAction = route('admin.ticket.view', $ticket->id);
		$this->adminFirebasePushNotification('SUPPORT_TICKET_CREATE', $msg, $firebaseAction);

		return redirect()->route('user.ticket.list')->with('success', 'Your Ticket has been pending');
	}

	public function view($ticketId)
	{
		$ticket = Ticket::with(['messages', 'user', 'lastReply'])->where('ticket', $ticketId)->latest()->with('messages')->first();
		$admin = Admin::first();

		$user = Auth::user();
		return view($this->theme . 'user.support.view', compact('ticket', 'user', 'admin'));
	}

	public function reply(Request $request, $id)
	{
		$ticket = Ticket::findOrFail($id);
		$message = new TicketMessage();

		if ($request->replayTicket == 1) {
			$images = $request->file('attachments');
			$allowedExtensions = array('jpg', 'png', 'jpeg', 'pdf');
			$this->validate($request, [
				'attachments' => [
					'max:4096',
					function ($fail) use ($images, $allowedExtensions) {
						foreach ($images as $img) {
							$ext = strtolower($img->getClientOriginalExtension());
							if (($img->getSize() / 1000000) > 2) {
								return $fail("Images MAX  2MB ALLOW!");
							}
							if (!in_array($ext, $allowedExtensions)) {
								return $fail("Only png, jpg, jpeg, pdf images are allowed");
							}
						}
						if (count($images) > 5) {
							return $fail("Maximum 5 images can be uploaded");
						}
					},
				],
				'message' => 'required',
			]);

			$ticket->status = 2;
			$ticket->last_reply = now();
			$ticket->save();

			$message->ticket_id = $ticket->id;
			$message->message = $request->message;
			$message->save();

			$path = config('location.ticket.path');

			if ($request->hasFile('attachments')) {
				foreach ($request->file('attachments') as $image) {
					try {
						$this->saveAttachment($message, $image, $path);
					} catch (\Exception $exp) {
						return back()->with('error', 'Could not upload your ' . $image)->withInput();
					}
				}
			}

			$msg = [
				'username' => optional($ticket->user)->username,
				'ticket_id' => $ticket->ticket
			];
			$action = [
				"link" => route('admin.ticket.view', $ticket->id),
				"icon" => "fas fa-ticket-alt text-white"
			];

			$this->adminPushNotification('SUPPORT_TICKET_REPLIED', $msg, $action);
			$firebaseAction = route('admin.ticket.view', $ticket->id);
			$this->adminFirebasePushNotification('SUPPORT_TICKET_REPLIED', $msg, $firebaseAction);

			return back()->with('success', 'Ticket has been replied');
		} elseif ($request->replayTicket == 2) {
			$ticket->status = 3;
			$ticket->last_reply = now();
			$ticket->save();

			return back()->with('success', 'Ticket has been closed');
		}
		return back();
	}

	public function download($ticket_id)
	{
		$attachment = TicketAttachment::findOrFail(decrypt($ticket_id));
		$file = $attachment->image;
		$full_path = getFile($attachment->driver, $file);
		$title = Str::slug($attachment->supportMessage->ticket->subject);
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
		header("Content-Type: " . $full_path);
		return readfile($full_path);
	}

	/**
	 * @param Request $request
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function newTicketValidation(Request $request)
	{
		$images = $request->file('attachments');
		$allowedExtension = array('jpg', 'png', 'jpeg', 'pdf');

		$this->validate($request, [
			'attachments' => [
				'max:4096',
				function ($attribute, $value, $fail) use ($images, $allowedExtension) {
					foreach ($images as $img) {
						$ext = strtolower($img->getClientOriginalExtension());
						if (($img->getSize() / 1000000) > 2) {
							return $fail("Images MAX  2MB ALLOW!");
						}
						if (!in_array($ext, $allowedExtension)) {
							return $fail("Only png, jpg, jpeg, pdf images are allowed");
						}
					}
					if (count($images) > 5) {
						return $fail("Maximum 5 images can be uploaded");
					}
				},
			],
			'subject' => 'required|max:100',
			'message' => 'required'
		]);
	}

	/**
	 * @param Request $request
	 * @param $random
	 * @return Ticket
	 */
	public function saveTicket(Request $request, $random)
	{
		$user = Auth::user();
		$ticket = new Ticket();
		$ticket->user_id = $user->id;
		$ticket->name = $user->username;
		$ticket->email = $user->email;
		$ticket->ticket = $random;
		$ticket->subject = $request->subject;
		$ticket->status = 0;
		$ticket->last_reply = now();
		$ticket->save();
		return $ticket;
	}

	/**
	 * @param Request $request
	 * @param $ticket
	 * @return TicketMessage
	 */
	public function saveMsgTicket(Request $request, $ticket)
	{
		$message = new TicketMessage();
		$message->ticket_id = $ticket->id;
		$message->message = $request->message;
		$message->save();
		return $message;
	}

	/**
	 * @param $message
	 * @param $image
	 * @param $path
	 * @throws \Exception
	 */
	public function saveAttachment($message, $image, $path)
	{
		$attachment = new TicketAttachment();
		$attachment->ticket_message_id = $message->id;
		$image = $this->fileUpload($image, $path);
		if ($image) {
			$attachment->image = $image['path'];
			$attachment->driver = $image['driver'];
		}
		$attachment->save();
	}
}
