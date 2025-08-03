# Use the official PHP image with Apache
FROM php:8.1-apache

# Copy your PHP files to the Apache web root
COPY . /var/www/html/

# Optional: Enable Apache rewrite if needed
RUN a2enmod rewrite
