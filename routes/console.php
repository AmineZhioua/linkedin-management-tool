<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ScheduleLinkedInPost;
use App\Models\ScheduledLinkedinPost;
use App\Console\Commands\PublishScheduledLinkedinPosts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
