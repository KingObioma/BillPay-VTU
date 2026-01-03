<?php

namespace App\Providers;

use App\Models\ContentDetails;
use App\Models\Gateway;
use App\Models\Language;
use App\Models\Template;
use App\Models\Ticket;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Paginator::useBootstrap();

		$data['basic'] = basicControl();
		$data['theme'] = template();
		$data['themeTrue'] = template(true);

		View::share($data);


		if (config('basic.force_ssl') == 1) {
			if ($this->app->environment('production') || $this->app->environment('local')) {
				\URL::forceScheme('https');
			}
		}

		try {
			DB::connection()->getPdo();

			view()->composer(['admin.ticket.nav', 'dashboard'], function ($view) {
				$view->with('pending', Ticket::whereIn('status', [0, 2])->latest()->with('user')->limit(10)->with('lastReply')->get());
			});

			view()->composer([
				$data['theme'] . 'partials.footer',
			], function ($view) {

				$languages = Language::orderBy('name')->where('is_active', 1)->get();
				$view->with('languages', $languages);

				$templateSection = ['contact'];
				$view->with('contactUs', Template::templateMedia()->whereIn('section_name', $templateSection)->first());


				$contentSection = ['social', 'pages'];
				$view->with('contentDetails', ContentDetails::select('id', 'content_id', 'description')
					->whereHas('content', function ($query) use ($contentSection) {
						return $query->whereIn('name', $contentSection);
					})
					->with(['content:id,name',
						'content.contentMedia' => function ($q) {
							$q->select(['content_id', 'description','driver']);
						}])
					->get()->groupBy('content.name'));
			});

			view()->composer($data['theme'] . 'sections.we-accept', function ($view) {
				$view->with('gateways', Gateway::where('status', 1)->orderBy('sort_by')->get());
			});

		} catch (\Exception $e) {
//			die("Could not connect to the database.  Please check your configuration according to documentation");
		}
	}
}
