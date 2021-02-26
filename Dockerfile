FROM leftsky/php

EXPOSE 433 80
WORKDIR /var/www


RUN ["/start.sh"]
