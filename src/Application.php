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

        $logger = new Logger();
        $logger->init();

        $sheet_helper = new SheetHelper();
        $domains_array = $sheet_helper->get_domains();
        $checked_domains_list = DomainsChecker::create_domain_list($domains_array);
        arsort($checked_domains_list);

        $suspected_domains = ArrayHelper::create_suspected_array($checked_domains_list);

        if (!empty($suspected_domains)) {
            $updated_domains = ArrayHelper::create_updated_array($checked_domains_list);
            
            $sheet_helper->clear_domains(count($domains_array));
            $sheet_helper->update_domains(array_keys($updated_domains));
            $sheet_helper->update_suspected_domains(array_keys($suspected_domains));
        }

        $logger->write_domains_to_log($checked_domains_list);

        if ($token != '' && $channel != '') {
            $slack = new Slack();
            $slack->send_to_slack($suspected_domains, $token, $channel);
        }

        if ($to != '') {
            $mailer = new Mailer();
            $mailer->send_to_mail($suspected_domains, $to);
        }

        return true;
    }
}