FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html
WORKDIR /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_ENV production

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

RUN chown -R xfs:xfs /var/www/html/storage /var/www/html/bootstrap/cache

# Overriding the default entry command using the image's native shell wrapper
CMD ["/bin/sh", "-c", "php artisan migrate --force && /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf"]