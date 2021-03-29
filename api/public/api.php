<?php

include_once __DIR__ . "/../vendor/autoload.php";
include_once __DIR__ . "/../util.php";

use Workerman\Worker;

$file_path = __DIR__ . "/../sites.json";

// 开启一个websocket协议服务
$ws_worker = new Worker("websocket://0.0.0.0:1300");
// 启动x个进程对外提供服务
$ws_worker->count = 2;
$ws_worker->onMessage = function ($con, $data) {
  $config = new NginxConfigJson();
  $data = json_decode($data, JSON_UNESCAPED_UNICODE);
  $cmd = $data['cmd'];
  $data = $data['data'];
  switch ($cmd) {
    case "getSites":
      // 获得所有网站信息
      $rt = $config->getSites();
      break;
    case "newSSL":
      // 申请SSL证书
      $domain = $data['domain'];
      $email = "admin@admin.com";
      system("/home/get_ssl.sh $domain $email");
      $rt = ['str' => '申请成功'];
      break;
    case "newSite":
      // 新增加一个网站的信息
      $site = [
        'domains' => [$data["domains"]],
        'vhost_dest_ip' => $data["dest_ip"],
        'name' => $data["name"],
        'use_ssl' => false,
        'ssl_publickey_file' => '',
        'ssl_privatekey_file' => ''
      ];
      if ($config->addSite($site)) {
        $rt = ['str' => '添加成功'];
      } else {
        $rt = ['str' => '已有此域名的网站'];
      }
      break;
    case "delSite":
      $config->delSite($data['domain']);
      // $config->save();
      // $rt =  $config->getSites();
      $rt = ['str' => '删除成功'];
      break;
    default:
      $rt = ['str' => 'hello world'];
      break;
  }
  return $con->send(json_encode($rt));
};
$ws_worker->onWorkerStart = function (Worker $worker) {
  // 当连接创建时
};

// 运行worker
Worker::$pidFile = __DIR__ . "/DataSender.pid";
Worker::runAll();
