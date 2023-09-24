<?php

namespace app;

require_once('src/web/CsvParser.php');
require_once('src/web/DomainsChecker.php');
require_once('src/log/Logger.php');

use app\web\CsvParser;
use app\web\DomainsChecker;
use app\log\Logger;

class App {
    private function init(): bool {
        date_default_timezone_set('Etc/GMT-3');
        if (ini_get('max_execution_time') >=30) {
            ini_set('max_execution_time', 900);
        }
        return true;
    }

    public function start($domains_list_url) {
        self::init();
        
        date_default_timezone_set('Etc/GMT-3');
        if (ini_get('max_execution_time') >=30) {
            ini_set('max_execution_time', 900);
        }
        $domains_array = CsvParser::get_domains_from_csv($domains_list_url);
        $http_array = DomainsChecker::check_domains($domains_array);
        
        arsort($http_array);
    
        $logger = new Logger();
        $logger->init();
        $logger->write_domains_to_log($http_array);
    }
}