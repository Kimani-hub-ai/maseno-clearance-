FROM richarvey/nginx-php-fpm:latest

# Copy your application code into the container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Configure Nginx to point to Laravel's public directory
ENV WEBROOT /var/www/html/public
ENV APP_ENV production

# Run composer installation for production optimization
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix permissions so the web server can read and write files
RUN chown -R xfs:xfs /var/www/html/storage /var/www/html/bootstrap/cache