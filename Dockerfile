FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html
WORKDIR /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_ENV production

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

RUN chown -R xfs:xfs /var/www/html/storage /var/www/html/bootstrap/cache

# Clears stale caches, ensures migrations execute, and boots the native start script safely
ENTRYPOINT ["/bin/sh", "-c", "php artisan config:clear && php artisan cache:clear && php artisan migrate --force && exec /start.sh"]