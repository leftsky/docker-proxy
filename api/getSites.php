<?php

require_once __DIR__ . "/util.php";


$config = file_get_contents($file_name);

// $site = [
//   'domains' => ["test.com"],
//   'vhost_dest_ip' => '127.0.0.1',
//   'use_ssl' => false,
//   'ssl_publickey_file' => '',
//   'ssl_privatekey_file' => ''
// ];

// echo put_proxy_file($site);

echo json_encode($config, JSON_UNESCAPED_UNICODE);
