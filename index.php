<?php

require_once('src/app.php');

use app\App;

$domains_list_url = $argv[1];

$app = new App();
$app->start($domains_list_url);