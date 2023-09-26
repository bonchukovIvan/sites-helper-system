<?php

namespace app;

use app\web\CsvParser;
use app\web\DomainsChecker;

use app\log\Logger;
use app\messangers\Mailer;
use app\helpers\ArrayHelper;
use app\messangers\Slack;

class Application {
    private function init(): bool {
        date_default_timezone_set('Etc/GMT-3');
        if (ini_get('max_execution_time') >= 30) {
            ini_set('max_execution_time', 600);
        }
        return true;
    }

    public function message($name) {
        return 'Hi, '. $name;
    }

    public function start($domains_list_url = null, $token = null, $channel = null, $to = null) {
        self::init();

        if (!$domains_list_url) {
            Logger::get_last_log();
            return true;
        }

        $logger = new Logger();
        $logger->init();

        $domains_array = CsvParser::get_domains_from_csv($domains_list_url);
        $checked_domains_list = DomainsChecker::create_domain_list($domains_array);
        arsort($checked_domains_list);

        $suspected_domains = ArrayHelper::create_suspected_array($checked_domains_list);

        $logger->write_domains_to_log($checked_domains_list);

        if ($token && $channel) {
            $slack = new Slack();
            $slack->send_to_slack($suspected_domains, $token, $channel);
        }

        if ($to) {
            $mailer = new Mailer();
            $mailer->send_to_mail($suspected_domains, $to);
        }

        return true;
    }
}