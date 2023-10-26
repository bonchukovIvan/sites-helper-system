<?php 

namespace app\helpers;

class ConfigHelper {

    private $config;

    public function __construct() {
        $config_path = dirname(__DIR__, 2) . '/' . 'configuration.ini';
        if (!file_exists($config_path)) {
           die('configuration file not found :('.PHP_EOL);
       }
       $this->config = parse_ini_file($config_path, true);
    }

    public function get_config() {
        return $this->config;
    }
}