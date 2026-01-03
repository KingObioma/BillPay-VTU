<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MasterTemplate extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;

	public $subject;
	public $message;
	public $notify_email;
	public $emailName;

	public function __construct($subject, $message, $notify_email, $emailName)
	{
		$this->subject = $subject;
		$this->message = $message;
		$this->notify_email = $notify_email;
		$this->emailName = $emailName;
	}

	public function build()
	{
		$subject = $this->subject;
		$message = $this->message;
		$notify_email = $this->notify_email;
		$emailName = $this->emailName;
		return $this->from($notify_email, $emailName)
			->subject($subject)
			->view('emails.master.template')
			->with(['template_body' => $message]);
	}
}
