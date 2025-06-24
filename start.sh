#!/bin/bash

# Dừng tất cả các tiến trình Laravel đang chạy
pkill -f "php artisan"

# Khởi động lại Beanstalkd
#sudo service beanstalkd start # Trên Ubuntu
#brew services start beanstalkd # Trên MacOS

# Khởi động server laravel và các worker queue
php artisan serve &
php artisan reverb:start &
php artisan queue:work redis --queue=default
