FROM trafex/php-nginx:3.6.0

# 1. Lumipat muna sa root user para makapag-install ng packages
USER root

# 2. I-install ang PHP 8.3 extensions na kailangan ng Laravel + Nodejs/NPM para sa Vue
RUN apk update && apk add --no-cache \
    php83-pdo_pgsql \
    php83-pgsql \
    php83-mbstring \
    php83-exif \
    php83-pcntl \
    php83-bcmath \
    php83-gd \
    php83-fileinfo \
    php83-xml \
    php83-dom \
    php83-tokenizer \
    php83-xmlwriter \
    php83-iconv \
    nodejs \
    npm

# 3. KOPYAHIN SI COMPOSER MULA SA OFFICIAL IMAGE (Ito ang nawawalang piraso!)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. I-set ang working directory (ito ang default public folder ng trafex image)
WORKDIR /var/www/html

# 5. Kopyahin ang buong project files mo
COPY --chown=nobody:nobody . .

# 6. I-install ang Composer at Node dependencies, tsaka i-build si Vue
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 7. I-configure si Nginx para ituro ang root sa /public folder ng Laravel
RUN sed -i 's|root /var/www/html;|root /var/www/html/public;|g' /etc/nginx/conf.d/default.conf

# 8. I-set ang tamang permissions para sa Laravel storage
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Balik sa safe (non-root) user na 'nobody' na siyang gamit ng image na ito
USER nobody