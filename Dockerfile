FROM richarvey/nginx-php-fpm:3.1.6

# 1. I-install si Node.js at NPM (Kailangan ito para ma-compile ang Vue/Inertia mo)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 2. I-set ang ENV variables na kailangan ng richarvey image para sa Laravel
ENV WEBROOT=/var/www/html/public
ENV APP_ENV=production

# 3. Kopyahin ang project files mo (sa image na ito, /var/www/html ang default folder)
COPY . /var/www/html

# 4. I-install ang PHP at Node dependencies, tapos i-build ang Vue assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 5. Ayusin ang permissions
RUN chown -R memcached:memcached /var/www/html/storage /var/www/html/bootstrap/cache