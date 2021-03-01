<?php

require_once __DIR__ . "/util.php";

$site = [
  'domains' => [$_GET["domains"]],
  'vhost_dest_ip' => $_GET["dest_ip"],
  'use_ssl' => false,
  'ssl_publickey_file' => '',
  'ssl_privatekey_file' => ''
];

put_proxy_file($site);
