<?php

namespace App\Jobs;

use App\Models\LinkedinCampaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CampaignStarted;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckCampaignStartStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $campaign;

    public function __construct(LinkedinCampaign $campaign)
    {
        $this->campaign = $campaign;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $now = Carbon::now();
            $startDate = Carbon::parse($this->campaign->start_date);

            if ($startDate <= $now) {
                event(new CampaignStarted($this->campaign));
            } else {
                $secondsUntilStart = $now->diffInSeconds($startDate, false);
                $this->release($secondsUntilStart > 60 ? 60 : $secondsUntilStart);
            }
        } catch (\Exception $e) {
            Log::error('Failed to check campaign start status', [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
