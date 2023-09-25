<?php

require_once('src/app.php');

use app\App;


@$domains_list_url = $argv[1] ? $argv[1] : null;
@$slack_token = $argv[2] ? $argv[2] : null;
@$slack_channel = $argv[3] ? $argv[3] : null;
@$mail_to = $argv[4] ? $argv[4] : null;

$app = new App();
$app->start($domains_list_url, $slack_token, $slack_channel, $mail_to);