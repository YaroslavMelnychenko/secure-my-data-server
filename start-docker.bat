docker-compose up -d
docker-compose exec php crontab /etc/cron.d/cron
docker-compose exec php php-fpm -D