<?php

namespace App\Events;

use App\Models\LinkedinCampaign;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $campaign;


    public function __construct(LinkedinCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('campaign-started.'.$this->campaign->user_id),
        ];
    }

    // New method to specify the event name
    public function broadcastAs() {
        return "CampaignStarted";
    }


    public function broadcastWith(): array
    {
        return [
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->campaign->user_id,
            'linkedin_user_id' => $this->campaign->linkedin_user_id,
            'event_name' => 'CampaignStarted',
            'message' => "Votre campagne '{$this->campaign->name}' a commencé à {$this->campaign->start_date}!"
        ];
    }
}
