<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-room', function ($user) {
    return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
});
