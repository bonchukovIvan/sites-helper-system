<?php

namespace app\log;

use UnexpectedValueException;

class Logger {

    const LOGS_DIR = '/logs';

    const LOGS_PREFIX = 'logs_';

    public function init(): bool {
        if (file_exists(dirname(__DIR__, 2) . self::LOGS_DIR)) {
            return true;
        }
        if (!mkdir(dirname(__DIR__, 2) . self::LOGS_DIR)) {
            die('Failed to create directory');
        }
        return true;
    }
    
    private function get_log_name(): string {
        return (dirname(__DIR__, 2) . self::LOGS_DIR) . '/' . self::LOGS_PREFIX . date('m.d.Y_H.i.s', time()).'.log';
    }

    public function write_domains_to_log($domains): bool {
        if (!is_array($domains)) {
            throw new UnexpectedValueException('Unexpected value type in params, domains must be array');
        }

        $log_message = '';
        foreach($domains as $key => $value) {
            $log_message .= $key . ' => Status code: ' . $value . PHP_EOL;
        }

        if (file_put_contents($this->get_log_name(), $log_message, FILE_APPEND)) {
            return true;
        };

        return false;
    }

    public static function get_last_log() {
        @$file = scandir(dirname(__DIR__, 2) . self::LOGS_DIR, SCANDIR_SORT_DESCENDING)[0];
        
        if (!$file) {
            echo 'Log directory empty :(' . PHP_EOL;
            return false;
        }
        print_r(file_get_contents(dirname(__DIR__, 2) . self::LOGS_DIR . '/' . $file));
        return true;
    }

    public static function test() {
        echo 'test'.PHP_EOL;
    }

}