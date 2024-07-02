#!/usr/bin/env bash
#@see https://laravel-news.com/laravel-scheduler-queue-docker
set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

if [ "$role" = "app" ]; then
    # echo "Caching configuration..."
    # (
    # php artisan optimize:clear \
    #   && rm -rf public/storage \
    #   && php artisan storage:link \
    #   && chown www-data:www-data storage/app/ -R \
    #   && php artisan optimize:clear
    # )
	echo "Running the app ..."
    php artisan octane:start --max-requests=1
    # exec php-fpm
    # exec apache2-foreground
elif [ "$role" = "queue" ]; then
    echo "Running the queue ..."
    # php artisan horizon
    php artisan queue:work --verbose --tries=3 --timeout=90
elif [ "$role" = "scheduler" ]; then
	echo "Running the scheduler ..."
    while [ true ]
    do
      php artisan schedule:run --verbose --no-interaction &
      sleep 30
    done
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
