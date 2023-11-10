<?php

namespace app;

use app\helpers\ConfigHelper;
use app\web\DomainsChecker;

use app\helpers\ArrayHelper;
use app\helpers\SheetHelper;

use app\log\Logger;

use app\messangers\Mailer;
use app\messangers\Slack;

class Application {

    private function init(): bool {
        date_default_timezone_set('Etc/GMT-3');
        if (ini_get('max_execution_time') >= 30) {
            ini_set('max_execution_time', 600);
        }

        return true;
    }

    public function start() {

        self::init();
        $config = new ConfigHelper();  

        $token = $config->get_config()['SLACK_TOKEN'];
        $channel = $config->get_config()['SLACK_CHANNEL'];
        $to = $config->get_config()['MAIL_TO'];

        $domains_cell = $config->get_config()['DOMAINS_CELL'];
        $emails_cell = $config->get_config()['EMAILS_CELL'];

        $logger = new Logger();
        $logger->init();
            
        $final_list = array();

        foreach($config->get_config()['RANGE_SETTINGS'] as $setting) {

            $all_domains_info = array();
            $sheet_helper = new SheetHelper($setting['range'], $setting['sheet']);
            $domains_array = $sheet_helper->get_domains($all_domains_info);

            $checked_domains_list = DomainsChecker::create_domain_list($domains_array);
            $final_list = array_merge($final_list, $checked_domains_list);
            arsort($checked_domains_list);
            
            $suspected_domains = ArrayHelper::create_suspected_array($checked_domains_list);

            $domains_email = array();

            foreach($suspected_domains as $domain => $status) {
                foreach($all_domains_info as $info) {
                    if($info[$domains_cell] === $domain) {
                        $domains_email[$info[$emails_cell]][$domain] = $status;
                        continue;
                    }
                }
            }

            asort($domains_email);
            
            if (!$suspected_domains) {
                continue;
            }

            if ($domains_email) {
                $mailer = new Mailer();
                foreach($domains_email as $email => $domains) {
                    if(!$email) {
                        $mailer->send_to_mail($domains, $to);
                        continue;
                    }
                    $mailer->send_to_mail($domains, $email);
                }
            }
        }            

        arsort($final_list);
        
        $logger->write_domains_to_log($final_list);
        
        if ($token != '' && $channel != '') {
            $suspected_domains = ArrayHelper::create_suspected_array($final_list);
            $slack = new Slack();
            $slack->send_to_slack($suspected_domains, $token, $channel);
        }

        return true;
    }
}