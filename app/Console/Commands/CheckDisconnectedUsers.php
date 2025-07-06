<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckDisconnectedUsers extends Command
{
    protected $signature = 'socket:check-timeouts';
    protected $description = 'Detect users that disconnected abruptly based on heartbeat timeout';

    public function handle()
    {
        $keys = Redis::keys('online-users:*');

        foreach ($keys as $key) {
            $lastSeen = Redis::get($key);
            if (!$lastSeen) continue;

            $lastSeenAt = Carbon::createFromTimestamp($lastSeen);
            if ($lastSeenAt->diffInSeconds(now()) > 60) {
                // user is considered disconnected
                [$_, $userId, $socketId] = explode(':', $key);

                Redis::del($key);

                // Xử lý gì đó, ví dụ log hoặc gui event thong bao cho admin or gui email thong bao
                \Log::warning("⛔ User {$userId} (socket {$socketId}) disconnected abruptly (timeout)");
            }
        }

        return Command::SUCCESS;
    }
}
