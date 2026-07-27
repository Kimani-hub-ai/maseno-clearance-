FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html
WORKDIR /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

RUN chown -R xfs:xfs /var/www/html/storage /var/www/html/bootstrap/cache

# Run config clear, migrations, seeders (firstOrCreate so safe to re-run),
# then optimize for production before booting the native start script
ENTRYPOINT ["/bin/sh", "-c", "\
    echo '--- ENV DEBUG ---'; \
    env; \
    echo '--- END DEBUG ---'; \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php artisan db:seed --class=DepartmentSeeder --force && \
    php artisan db:seed --class=UserSeeder --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    exec /start.sh"]