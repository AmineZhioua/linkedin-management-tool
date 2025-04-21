<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('post_max_size', env('POST_MAX_SIZE', '100M'));
        ini_set('upload_max_filesize', env('UPLOAD_MAX_FILESIZE', '100M'));
    }
}
