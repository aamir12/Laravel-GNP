#!/usr/bin/env bash

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/app storage/logs
chown -R www-data:www-data storage

if [ "$env" != "development" ] && [ "$env" != "testing" ]; then
    composer install --optimize-autoloader --no-dev

    echo "Caching configuration..."
    php artisan config:cache && php artisan route:cache && php artisan view:cache
else
    composer install
fi

if [ "$role" = "app" ]; then

    php artisan migrate --force
    if [ ! -f "/var/www/html/storage/oauth-public.key" ] || [ ! -f "/var/www/html/storage/oauth-private.key" ]; then
        php artisan passport:install
    fi
    chown -R www-data:www-data storage

    # Install dependencies and build frontend bundle files.
    npm install
    npm run prod

    # Any public folders you want accessible need to be copied to the /var/www/html folder.
    # If you want the volume mounts to take effect you should symlink them using `ln -s`
    # rsync -a public/js /var/www/html # Move all built js to our root
    # rsync -a public/css /var/www/html # Move all built css to our root

    # Generate API Documentation and move all the generated docs to our root.
    # NOTE: API docs must be generated AFTER the app has been properly set up (e.g. migrations and OAuth keys).
    # This is because the API doc generator will send requests to our API in order to generate example responses.
    php artisan scribe:generate

    echo "$IOS_DEEPLINK" > /var/www/html/public/.well-known/apple-app-site-association
    echo "$ANDROID_DEEPLINK" > /var/www/html/public/.well-known/assetlinks.json

    # Execute scripts which are defined in the php:7.4-apache image.
    exec docker-php-entrypoint apache2-foreground

elif [ "$role" = "scheduler" ]; then

    touch /var/log/start.log /var/log/end.log
    echo "Running the scheduler..."
    php artisan schedule:work --verbose

elif [ "$role" = "queue" ]; then

    echo "Running the queue..."
    php artisan queue:work --verbose --tries=3 --timeout=90

else

    echo "Could not match the container role \"$role\""
    exit 1

fi
