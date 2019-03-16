FROM agentejo/cockpit:latest

ENV PORT ${PORT:-80}
EXPOSE $PORT 80 443

COPY app /var/www/html
COPY app/conf/config.php /var/www/html/config/config.php
RUN chown -R www-data:www-data /var/www/html

