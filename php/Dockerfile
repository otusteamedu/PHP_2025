FROM nanoninja/php-fpm:8.1

RUN echo "session.save_handler = redis" >> /usr/local/etc/php/conf.d/redis-session.ini
RUN echo "session.save_path = 'tcp://redis:6379'" >> /usr/local/etc/php/conf.d/redis-session.ini

WORKDIR /var/www/html