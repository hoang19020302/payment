<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    /**
     * Lưu key-value đơn giản với thời gian sống tùy chọn
     */
    public static function set(string $key, mixed $value, int $ttl = null): void
    {
        if ($ttl) {
            Redis::setex($key, $ttl, $value);
        } else {
            Redis::set($key, $value);
        }
    }

    /**
     * Lấy giá trị theo key
     */
    public static function get(string $key): mixed
    {
        return Redis::get($key);
    }

    /**
     * Xóa key
     */
    public static function delete(string $key): void
    {
        Redis::del($key);
    }

    /**
     * Gán giá trị trong hash
     */
    public static function hset(string $hash, string $field, mixed $value): void
    {
        Redis::hset($hash, $field, is_array($value) ? json_encode($value) : $value);
    }

    /**
     * Lấy giá trị từ hash
     */
    public static function hget(string $hash, string $field): mixed
    {
        return Redis::hget($hash, $field);
    }

    /**
     * Lấy toàn bộ giá trị trong hash
     */
    public static function hgetAll(string $hash): array
    {
        return Redis::hgetall($hash);
    }

    /**
     * Xóa field trong hash
     */
    public static function hdel(string $hash, string $field): void
    {
        Redis::hdel($hash, $field);
    }

    /**
     * Thêm phần tử vào set (không trùng)
     */
    public static function sadd(string $key, mixed $value): void
    {
        Redis::sadd($key, $value);
    }

    /**
     * Kiểm tra phần tử có trong set không
     */
    public static function sismember(string $key, mixed $value): bool
    {
        return Redis::sismember($key, $value);
    }

    /**
     * Lấy toàn bộ phần tử trong set
     */
    public static function smembers(string $key): array
    {
        return Redis::smembers($key);
    }

    /**
     * Xóa phần tử khỏi set
     */
    public static function srem(string $key, mixed $value): void
    {
        Redis::srem($key, $value);
    }

    /**
     * Tăng giá trị
     */
    public static function increment(string $key, int $amount = 1): void
    {
        Redis::incrby($key, $amount);
    }

    /**
     * Giảm giá trị
     */
    public static function decrement(string $key, int $amount = 1): void
    {
        Redis::decrby($key, $amount);
    }

    /**
     * Đặt thời gian sống cho key
     */
    public static function expire(string $key, int $seconds): void
    {
        Redis::expire($key, $seconds);
    }

    /**
     * Tạo lock đơn giản
     */
    public static function acquireLock(string $key, int $ttl = 30): bool
    {
        if (Redis::setnx($key, now()->timestamp)) {
            Redis::expire($key, $ttl);
            return true;
        }
        return false;
    }

    /**
     * Xóa lock
     */
    public static function releaseLock(string $key): void
    {
        Redis::del($key);
    }

    /**
     * Tìm key theo pattern (tránh dùng production lớn)
     */
    public static function findKeys(string $pattern = '*'): array
    {
        return Redis::keys($pattern);
    }
}
