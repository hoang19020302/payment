#!/bin/sh

echo "Starting Laravel Octane (Swoole)..."
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
