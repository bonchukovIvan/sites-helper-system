<?php

namespace app\messangers;

class BaseMessanger {

    const LANGUAGE_PATH = 'src/language/uk_UA/uk_ua.main.ini';
    protected $lang_section = '';
    protected function get_localization() {
        if(!file_exists(self::LANGUAGE_PATH)){
            return false;
        };
        return parse_ini_file(self::LANGUAGE_PATH, true)[$this->lang_section];
    }

    public function create_report_message($suspected_domains): string {
        if (!$suspected_domains) { 
            return self::get_localization()['REPORT_DONE_TITLE']; 
        } 

        $report_message = ''; 
        $report_message .= date('m/d/Y h:m', time()) . ' ' . self::get_localization()['REPORT_TITLE']. ' ' . self::get_localization()['REPORT_TO']  . PHP_EOL;
        $report_message .=  self::get_localization()['REPORT_SITES_WITH_ERROR'] . ': ' . count($suspected_domains) . PHP_EOL; 
        foreach($suspected_domains as $key => $value) {
            $report_message .= $key . ' => ' . $value .PHP_EOL; 
        }

        return $report_message; 
    } 
}