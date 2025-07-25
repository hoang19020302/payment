<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class Presence
{
    protected static function key(string $channel): string
    {
        return "presence:{$channel}:members";
    }

    public static function join(string $channel, array $user): void
    {
        //$field = "{$user['id']}:{$socketId}";
        Redis::hset(self::key($channel), (string) $user['id'], json_encode([
            'id' => $user['id'],
            'name' => $user['name'] ?? 'Unknown',
            'joined_at' => Carbon::now()->toISOString(),
        ]));
        Redis::expire(self::key($channel), 600);
    }

    public static function leave(string $channel, int|string $userId): void
    {
        Redis::hdel(self::key($channel), (string) $userId);
    }

    public static function countUsers(string $channel): int
    {
        $members = Redis::hkeys(self::key($channel));

        return collect($members)
            ->map(fn($key) => explode(':', $key)[0])
            ->unique()
            ->count();
    }


    public static function all(string $channel): array
    {
        $members = Redis::hgetall(self::key($channel));

        return collect($members)->map(function ($item) {
            return json_decode($item, true);
        })->values()->toArray();
    }

    public static function sync(string $channel, array $users): void
    {
        Redis::del(self::key($channel));

        foreach ($users as $user) {
            Redis::hset(self::key($channel), (string) $user['id'], json_encode([
                'id' => $user['id'],
                'name' => $user['name'] ?? 'Unknown',
                'joined_at' => Carbon::now()->toISOString(),
            ]));
        }

        Redis::expire(self::key($channel), 600);
    }

    public static function refresh(string $channel): void
    {
        Redis::expire(self::key($channel), 600);
    }

    public static function find(string $channel, int|string $userId): ?array
    {
        return Redis::hget(self::key($channel), (string) $userId);
    }

    public static function delete(string $channel, int|string $userId): void
    {
        Redis::hdel(self::key($channel), (string) $userId);
    }

    public static function count(string $channel): int
    {
        return Redis::hlen(self::key($channel));
    }

    public static function clear(string $channel): void
    {
        Redis::del(self::key($channel));
    }
}
