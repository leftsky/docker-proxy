<?php

include_once __DIR__ . "/../vendor/autoload.php";

use Workerman\Worker;

// 注意：这里与上个例子不同，使用的是websocket协议
$ws_worker = new Worker("websocket://0.0.0.0:1300");
// 启动x个进程对外提供服务
$ws_worker->count = 2;
$ws_worker->onMessage = function ($con, $data) {
  $data = json_decode($data, JSON_UNESCAPED_UNICODE);
  switch ($data['cmd']) {
    case "getBlogs":
      $rt = [[
        'name' => '个人博客',
        'ip' => '192.168.191.2',
        'ssl_status' => '正常 2021-12-01 到期'
      ], []];
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
