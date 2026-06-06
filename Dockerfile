FROM richarvey/nginx-php-fpm:php83-latest

# 1. I-install si Node.js at NPM gamit ang 'apk' (Alpine Package Manager)
RUN apk update && apk add --no-cache nodejs npm

# 2. Ituro ang Nginx Root sa Public folder ng Laravel
ENV WEBROOT=/var/www/html/public
ENV APP_ENV=production

# 3. Kopyahin ang code mo papunta sa loob ng container
COPY . /var/www/html

# 4. I-install ang Composer at NPM packages, tsaka i-build si Vue
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 5. Ayusin ang permissions ng Laravel folders (In-adjust para sa Alpine user group)
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache