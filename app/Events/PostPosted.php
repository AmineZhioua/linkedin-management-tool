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

class PostPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $post;
    public $campaign;

    public function __construct(ScheduledLinkedinPost $post, LinkedinCampaign $campaign)
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
            new PrivateChannel('post-posted.'.$this->post->user_id),
        ];
    }

    public function broadcastAs() {
        return "PostPosted";
    }


    public function broadcastWith(): array
    {
        return [
            'message' => "Campaign {$this->campaign->name}: Post {$this->post->id} de type {$this->post->type} a été publié avec succès !",
        ];
    }
}
