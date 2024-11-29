# PHP Apache 이미지를 기반으로 함
FROM php:8.1-apache

# 필요한 PHP 확장과 도구들 설치
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

# Apache 모듈 활성화
RUN a2enmod rewrite

# Apache 설정
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Apache Directory 설정 수정
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# PHP 설정
RUN echo "display_errors = Off" >> /usr/local/etc/php/php.ini-production \
    && echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/php.ini-production \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 파일 권한 설정
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
