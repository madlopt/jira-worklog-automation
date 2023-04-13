FROM php:8.2-alpine

RUN apk add --no-cache yaml-dev autoconf build-base \
    && pecl install yaml \
    && docker-php-ext-enable yaml

# Install crond and some basic utilities
RUN apk --no-cache add busybox-suid bash curl
# Copy the crontab file to the container
COPY crontab.txt /etc/crontabs/root
# Start crond in the foreground
CMD ["crond", "-f"]

COPY . /var/www/worklog

RUN mkdir -p /var/www/worklog/var/log \
    && chmod 0777 /var/www/worklog/var/log
RUN touch /var/www/worklog/var/log/cronjob.log \
    && chmod 0777 /var/www/worklog/var/log/cronjob.log

WORKDIR /var/www/worklog

RUN chmod -R 755 /var/www/worklog
RUN mkdir -p /var/www/worklog/vendor \
    && chmod -R 777 /var/www/worklog/vendor

RUN php composer.phar install --no-scripts --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader


