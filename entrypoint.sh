#!/bin/sh

set -e

echo "🧩 Laravel entrypoint: cache & permissions"

# Cache config và routes
php artisan config:clear || true
php artisan route:clear || true

php artisan config:cache || true
php artisan route:cache || true

# Tạo symlink nếu cần
php artisan storage:link || true

# Phân quyền
chmod -R 775 storage bootstrap/cache

echo "✅ Laravel ready!"

# Thực thi lệnh CMD của container (FrankenPHP sẽ tự chạy)
exec "$@"
