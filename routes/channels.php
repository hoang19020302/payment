<?php

use Illuminate\Support\Facades\Broadcast;

// Auth::routes();
// Broadcast::auth(function ($request) {
//     $user = $request->user();

//     if (!$user) {
//         return false;
//     }

//     if ($user->is_banned) {
//         abort(403, 'You are banned');
//     }

//     return ['id' => $user->id, 'name' => $user->name];
// });

// Broadcast::auth(function ($request) {
//     $user = $request->user();

//     if (!$user) {
//         return false;
//     }

//     // Check trong Redis
//     $banned = Redis::get('banned:user:' . $user->id) || $user->is_banned;

//     if ($banned) {
//         abort(403, 'You are banned');
//     }

//     return ['id' => $user->id, 'name' => $user->name];
// });


Broadcast::channel('chat-room', function ($user) {
    // if ($user->is_banned) {
    //     return false;
    // }
    return ['id' => $user->id, 'name' => $user->name];
});
