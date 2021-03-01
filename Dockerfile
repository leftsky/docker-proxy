FROM leftsky/php
LABEL maintainer="leftsky <leftsky@vip.qq.com>" 

COPY get_ssl.sh /home/
COPY api /var/www/public
RUN chmod a+x /home/get_ssl.sh
RUN apk add certbot tcl tk expect
RUN mkdir -p /var/www/public/.well-known/acme-challenge/

EXPOSE 433 80
WORKDIR /var/www
