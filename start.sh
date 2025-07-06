#!/bin/bash

# Dừng tất cả các tiến trình Laravel đang chạy
pkill -f "php artisan"

# Khởi động lại Beanstalkd
#sudo service beanstalkd start # Trên Ubuntu
#brew services start beanstalkd # Trên MacOS

# Khởi động server laravel và các worker queue
#php artisan serve &
php artisan octane:frankenphp --host=0.0.0.0 --port=8000 &
# php artisan reverb:start &
php artisan queue:work redis --queue=default --tries=3 --sleep=1 &
php artisan queue:work beanstalkd --queue=webhooks --tries=3 --sleep=3

#php artisan octane:cache:clear
#php artisan octane:cache
