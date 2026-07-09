FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html
WORKDIR /var/www/html

ENV WEBROOT /var/www/html/public
ENV APP_ENV production

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

RUN chown -R xfs:xfs /var/www/html/storage /var/www/html/bootstrap/cache

# Give execute permissions to our script and set it to run on boot
RUN chmod +x /var/www/html/entrypoint.sh
CMD ["/var/www/html/entrypoint.sh"]