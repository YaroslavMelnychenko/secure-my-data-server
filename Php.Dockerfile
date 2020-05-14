FROM php:7.2-fpm

RUN apt-get update -y
RUN apt-get install libsodium-dev -y
RUN apt-get install nano
RUN docker-php-ext-install pdo pdo_mysql sodium

RUN apt-get install -y cron

ADD schedule/crontab /etc/cron.d/cron

RUN chmod 0644 /etc/cron.d/cron

RUN touch /var/log/cron.log

CMD printenv > /etc/environment && echo "cron starting..." && (cron) && : > /var/log/cron.log && tail -f /var/log/cron.log ; crontab /etc/cron.d/cron