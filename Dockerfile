FROM nginx:alpine
LABEL maintainer="leftsky <leftsky@vip.qq.com>" 

# 复制代码
COPY api /var/api
COPY leftsky-dashboard/build /usr/share/nginx/html
# 复制申请SSL的shell文件
COPY get_ssl.sh /home/
RUN chmod a+x /home/get_ssl.sh
# 复制启动文件
COPY start.sh /start.sh
RUN chmod 755 /start.sh
# 复制nginx配置文件
COPY default.conf /etc/nginx/conf.d/
# 复制 supervisor 文件
COPY supervisord.conf /etc/supervisord.conf

# 更新软件包
RUN echo "#aliyun" > /etc/apk/repositories
RUN echo "https://mirrors.aliyun.com/alpine/v3.12/main/" >> /etc/apk/repositories
RUN echo "https://mirrors.aliyun.com/alpine/v3.12/community/" >> /etc/apk/repositories
RUN apk update
RUN apk add php7 php7-posix php7-pcntl php7-json
# 安装更新软件
RUN apk add --update bash supervisor

RUN apk add certbot tcl tk expect
RUN mkdir -p /var/www/public/.well-known/acme-challenge/
RUN mkdir -p /var/www/certbot/logs
RUN mkdir -p /var/www/certbot/config
RUN mkdir -p /var/www/certbot/work
RUN chown -R nginx:nginx /var/www/certbot

EXPOSE 1300 443
WORKDIR /var/www

CMD ["/start.sh"]

