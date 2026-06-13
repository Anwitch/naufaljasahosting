FROM php:8.2-apache

# Install PHP extensions & enable Apache modules
RUN docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite headers

# Copy application files
COPY portal.html /var/www/html/index.html
COPY portal.html /var/www/html/portal.html
COPY webgis_app/ /var/www/html/webgis_app/
COPY db_scripts/ /var/www/html/db_scripts/
COPY 01/ /var/www/html/01/
COPY 02/ /var/www/html/02/
COPY 03/ /var/www/html/03/

# Copy db schema to where submodules expect it
RUN mkdir -p /var/www/html/database && cp /var/www/html/db_scripts/webgis_naufal_zaky.sql /var/www/html/database/webgis_db.sql

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 80
