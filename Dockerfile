FROM leftsky/php
LABEL maintainer="leftsky <leftsky@vip.qq.com>" 

COPY get_ssl.sh /home/
RUN chmod a+x /home/get_ssl.sh
RUN apk add certbot tcl tk expect

EXPOSE 433 80
WORKDIR /var/www
