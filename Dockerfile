FROM trafex/php-nginx:3.6.0


USER root


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


COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-merge-driver.js /usr/local/bin/npm-merge-driver \
    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY --chown=nobody:nobody . .


RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

RUN sed -i 's|root /var/www/html;|root /var/www/html/public;|g' /etc/nginx/conf.d/default.conf


RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache

USER nobody

CMD if [ ! -f /tmp/seeded.txt ]; then \
    php artisan migrate:fresh --seed --force && touch /tmp/seeded.txt; \
    fi && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf