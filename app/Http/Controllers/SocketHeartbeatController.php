<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SocketHeartbeatController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'socket_id' => 'required|string',
        ]);

        $key = "online-users:{$request->user_id}:{$request->socket_id}";
        Redis::setex($key, 90, now()->timestamp); // TTL 90s

        return response()->json(['status' => 'ok']);
    }
}


