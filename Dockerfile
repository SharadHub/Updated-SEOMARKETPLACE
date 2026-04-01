FROM php:8.2-apache

# Install mysqli extension for MySQL connectivity
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Copy project files to Apache web root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port (Render will set $PORT)
ENV PORT=80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
