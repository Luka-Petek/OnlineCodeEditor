# Uporabimo Alpine sliko za manjšo velikost in hitrejšo izgradnjo
# php:8.3-fpm-alpine je dobra izbira
FROM php:8.3-fpm-alpine

# Nastavimo delovni direktorij
WORKDIR /var/www/html

# Uporabimo Alpine package manager (apk) za namestitev docker clienta.
# Ta klient bo uporabil vpeti /var/run/docker.sock za komunikacijo z zunanjim motorjem.
RUN apk add --no-cache \
    docker \
    bash \
    git \
    make

# Prikaz in omogočanje razširitve opcache za boljšo zmogljivost
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.use_cwd=1'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.max_accelerated_files=10000'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Kopirajte konfiguracijske datoteke, če jih imate (npr. php.ini)
# COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Kopirajte PHP kodo v kontejner (ta del ponavadi obravnavate z volumni v docker-compose)
# Če ne uporabljate volumnov, bi to bil pravi čas za COPY
# COPY ./data/www/html /var/www/html

# Uporabite standardni FPM ukaz za zagon
CMD ["php-fpm"]