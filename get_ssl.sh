#!/usr/bin/expect

# 拉取域名参数
set domains [lindex $argv 0]
# 拉取邮箱参数
set email [lindex $argv 1]
# 设置超时
set timeout 30
# 开始申请证书
spawn certbot --server https://acme-v02.api.letsencrypt.org/directory \
--logs-dir=/var/www/certbot/logs \
--config-dir=/var/www/certbot/config \
--work-dir=/var/www/certbot/work \
-d "$domains" --manual --preferred-challenges http-01 certonly
# 输入邮箱，同意获得IP
expect "Enter email address" { send "$email\r"; exp_continue; } \
"agree in order to register with the ACME server" { send "A\r"; exp_continue; } \
"share your email" { send "N\r"; exp_continue; } \
"Are you OK with your IP being logged" { send "Y\r"; } \
"Renew & replace the cert" { send "2\r"; }
# 获得验证文件内容
expect -re "(\\S{70,90})"
set file_content $expect_out(0,string)
# expect -re "(http\\S{40,90})"
# set file_url $expect_out(0,string)
expect -re "http\\S+/(\\S{10,90})"
# send_user $expect_out(0,string)
# send_user $expect_out(1,string)
set file_name $expect_out(1,string)
send_user "\r\n校验文件内容：$file_content"
# send_user "\r\n校验文件路径：$file_url"
send_user "\r\n校验文件名：$file_name"
exec echo "$file_content" > /var/www/public/.well-known/acme-challenge/$file_name
send "\r"
interact

