<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('campaign-started.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('post-posted.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
