FROM php:8.5-cli

# PHP Extensions
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libpq-dev libcurl4-openssl-dev libxml2-dev libonig-dev \
	&& rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install -j1 pdo pdo_pgsql curl mbstring xml

WORKDIR /var/www/html

COPY .docker/backend/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
