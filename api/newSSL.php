<?php

$domains = $_GET["domains"];
$email = $_GET["email"];
system("/home/get_ssl.sh $domains $email");
