<?php

namespace App\Providers;

use App\Models\ContentDetails;
use App\Models\ContentMedia;
use App\Models\Language;
use App\Models\Template;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class BasicServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            DB::connection()->getPDO();
            view()->composer(['frontend.layouts.topbar', 'user.layouts.topbar'], function ($view) {
                $getLanguages = Language::where('is_active', 1)->orderBy('name', "ASC")->get();
                $view->with(['getLanguages' => $getLanguages]);
            });

            view()->composer(['frontend.layouts.master', 'frontend.layouts.scripts', 'frontend.layouts.styles'], function ($view) {
                $basicControl = basicControl();
                $view->with(['basicControl' => $basicControl]);
            });

            view()->composer('frontend.layouts.footer', function ($view) {
                $socialLinks = ContentMedia::with('content')
                    ->whereHas('content', function ($query) {
                        $query->where('name', 'social-links');
                    })->get();

                $contactDetails = ContentDetails::with(['content', 'content.contentMedia'])
                    ->whereHas('content', function ($query) {
                        $query->where('name', 'contact');
                    })->orderByDesc('content_id')->get()->take(3);

                $templates = Template::whereIn('section_name', ['about-us', 'subscribe'])->get()->groupBy('section_name');

                $view->with([
                    'socialLinks' => $socialLinks,
                    'contactDetails' => $contactDetails,
                    'templates' => $templates,
                ]);
            });
        } catch (\Exception $e) {

        }
    }
}
