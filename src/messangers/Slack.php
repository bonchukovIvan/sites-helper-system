<?php

namespace app\messangers;


use app\messangers\BaseMessanger;

class Slack extends BaseMessanger {

    protected $lang_section = 'slack_section';
    function send_to_slack($suspected_domains, $token, $channel){ 
        $message = self::create_report_message($suspected_domains, true);
        $ch = curl_init("https://slack.com/api/chat.postMessage"); 
        $data = http_build_query([ 
            "token" => $token, 
            "channel" => '#' . $channel, 
            "text" => $message, 
            "username" => "sites-alarm", 
        ]); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $result = curl_exec($ch); 
        curl_close($ch); 
        return $result; 
    } 
}
