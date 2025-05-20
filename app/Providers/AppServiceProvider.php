<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Add this import

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
        Paginator::useBootstrapFive();
        ini_set('post_max_size', env('POST_MAX_SIZE', '100M'));
        ini_set('upload_max_filesize', env('UPLOAD_MAX_FILESIZE', '100M'));
    }
}
