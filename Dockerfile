FROM trafex/php-nginx:3.6.0

# 1. Lumipat muna sa root user para makapag-install ng packages
USER root

# 2. I-install ang PHP 8.3 extensions
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
    php83-iconv

# 3. Kopyahin ang Node 22 at Composer mula sa official images
COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-merge-driver.js /usr/local/bin/npm-merge-driver \
    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. I-set ang working directory
WORKDIR /var/www/html

# 5. Kopyahin ang buong project files mo (I-set agad kay nobody ang pag-aari)
COPY --chown=nobody:nobody . .

# 6. I-install ang Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. I-install ang NPM packages at i-build ang Vite assets gamit ang user na 'nobody'
# Ito ang sikreto para hindi maging 'root' ang may-ari ng manifest file!
USER nobody
RUN npm install
RUN npm run build

# 8. Bumalik sandali sa root para lang i-configure ang Nginx at huling permissions
USER root
RUN sed -i 's|root /var/www/html;|root /var/www/html/public;|g' /etc/nginx/conf.d/default.conf
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# 9. I-set ang permanenteng user sa 'nobody' para sa pagpapatakbo ng app
USER nobody

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]