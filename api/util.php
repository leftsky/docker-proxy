<?php

// nginx虚拟主机配置文件目录位置
$sites_config_dir = "/etc/nginx/conf.d/";
// 检查nginx虚拟主机配置目录是否存在，如果不存在则退出
if (!file_exists($sites_config_dir)) {
  exit("虚拟主机配置文件目录不存在");
}

// 根据配置信息生成虚拟目录配置文件方法
function put_proxy_file($site)
{
  global $sites_config_dir;
  if (sizeof($site["domains"]) <= 0 || $site["vhost_dest_ip"] == "") {
    return false;
  }
  // 组装文件内容
  $vhost_config_content = "";
  // 监听80端口
  $vhost_config_content .= "listen 80;\r\n";
  // 如果使用了ssl，监听443端口
  if ($site['use_ssl']) {
    $vhost_config_content .= "listen 443 ssl;\r\n";
  }
  // 允许最大大小50M
  $vhost_config_content .= "client_max_body_size 50M;\r\n";
  // 绑定所有域名
  $vhost_config_content .= "server_name";
  foreach ($site['domains'] as $domain) {
    $vhost_config_content .= " " . $domain;
  }
  $vhost_config_content .= ";\r\n";
  // 配置SSL密钥文件位置，增加SSL配置支持
  if ($site['use_ssl']) {
    $vhost_config_content .= "ssl_certificate {$site['ssl_publickey_file']};
ssl_certificate_key {$site['ssl_privatekey_file']};
ssl_protocols TLSv1.1 TLSv1.2 TLSv1.3;
ssl_ciphers EECDH+CHACHA20:EECDH+CHACHA20-draft:EECDH+AES128:RSA+AES128:EECDH+AES256:RSA+AES256:EECDH+3DES:RSA+3DES:!MD5;
ssl_prefer_server_ciphers on;
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
add_header Strict-Transport-Security \"max-age=31536000\";\r\n";
  }
  // 配置不转发.wellknow文件夹
  $vhost_config_content .= "location ~ /\.wellknow { allow all; }\r\n";
  // 配置转发ip
  $vhost_config_content .= "location / {
  proxy_pass http://{$site['vhost_dest_ip']};
}
error_page   500 502 503 504  /50x.html;
location = /50x.html {
 root   /usr/share/nginx/html;
}";

  $file_content = "server {\r\n$vhost_config_content\r\n}";
  file_put_contents($sites_config_dir . $site['domains'][0] . ".conf", $file_content);
  return $file_content;
}

class NginxConfigJson
{
  private $file_name = __DIR__ . "/nginx_proxy_config.json";
  private $config = [];

  function __construct()
  {
    if (!file_exists($this->file_name)) {
      file_put_contents($this->file_name, json_encode([
        'str' => 'nginx config',
        'sites' => []
      ], JSON_UNESCAPED_UNICODE));
    }
    $this->config = json_decode(file_get_contents($this->file_name), true);
  }

  function __destruct()
  {
    $this->save();
    // array_map('unlink', glob($sites_config_dir . "/*.conf"));
    system('nginx -s reload');
    foreach ($this->config['sites'] as $site) {
      put_proxy_file($site);
    }
  }

  function save()
  {
    file_put_contents($this->file_name, json_encode($this->config, JSON_UNESCAPED_UNICODE));
  }

  function getSites()
  {
    return $this->config['sites'];
  }

  function addSite($site)
  {
    foreach ($this->config['sites'] as $s) {
      if ($s['domains'] == $site['domains']) {
        return false;
      }
    }
    $this->config['sites'][] = $site;
    return true;
  }

  function delSite($domain)
  {
    global $sites_config_dir;
    $arr = [];
    foreach ($this->config['sites'] as $s) {
      if ($s['domains'][0] != $domain) {
        $arr[] = $s;
      } else {
        array_map('unlink', glob("$sites_config_dir/$domain.conf"));
      }
    }
    $this->config['sites'] = $arr;
    return true;
  }
};
