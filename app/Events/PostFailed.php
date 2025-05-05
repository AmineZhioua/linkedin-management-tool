<?php

namespace App\Events;

use App\Models\LinkedinCampaign;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $post;
    public $campaign;

    public function __construct(LinkedinCampaign $campaign, ScheduledLinkedinPost $post)
    {
        $this->post = $post;
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
            new PrivateChannel('post-failed.'.$this->post->user_id),
        ];
    }


    public function broadcastAs() {
        return "PostFailed";
    }


    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->post->user_id,
            'campaign_id' => $this->campaign->id,
            'linkedin_user_id' => $this->post->linkedin_user_id,
            'event_name' => 'PostFailed',
            'message' => "Campagne {$this->campaign->name}: Post {$this->post->id} de type {$this->post->type} a échoué lors de la publication !",
        ];
    }
}
