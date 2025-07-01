<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class PresenceService
{
    protected function key(string $channel): string
    {
        return "presence:{$channel}:members";
    }

    /**
     * Thêm user vào channel
     */
    public function join(string $channel, array $user): void
    {
        $key = $this->key($channel);
        Redis::hset($key, (string) $user['id'], json_encode([
            'id' => $user['id'],
            'name' => $user['name'] ?? 'Unknown',
            'joined_at' => Carbon::now()->toISOString(),
        ]));

        // Đặt TTL cho key (tùy chọn, ví dụ 10 phút)
        Redis::expire($key, 600);
    }

    /**
     * Xoá user khỏi channel
     */
    public function leave(string $channel, int|string $userId): void
    {
        $key = $this->key($channel);
        Redis::hdel($key, (string) $userId);
    }

    /**
     * Lấy danh sách tất cả user trong channel
     */
    public function all(string $channel): array
    {
        $key = $this->key($channel);
        $members = Redis::hgetall($key);

        return collect($members)->map(function ($item) {
            return json_decode($item, true);
        })->values()->toArray();
    }

    /**
     * Đếm số user trong channel
     */
    public function count(string $channel): int
    {
        return Redis::hlen($this->key($channel));
    }

    /**
     * Xoá toàn bộ channel
     */
    public function clear(string $channel): void
    {
        Redis::del($this->key($channel));
    }

    /**
     * Sync lại toàn bộ user
     */
    public function sync(string $channel, array $users): void
    {
        $key = $this->key($channel);
        Redis::del($key);

        foreach ($users as $user) {
            Redis::hset($key, (string) $user['id'], json_encode([
                'id' => $user['id'],
                'name' => $user['name'] ?? 'Unknown',
                'joined_at' => Carbon::now()->toISOString(),
            ]));
        }

        Redis::expire($key, 600);
    }
}
// # Xem danh sách user trong chat-room
// HGETALL presence:chat-room:members

# Đếm số user
// HLEN presence:chat-room:members