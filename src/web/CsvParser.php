<?php

namespace app\web;

require_once('src/helpers/StringHelper.php');

use app\helpers\StringHelper;

class CsvParser {

    const SUB_DOMAIN = '.edu.ua';

    public static function get_domains_from_csv($csv_url): array {
        $csv_data = '';
        if (($handle = fopen($csv_url, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                foreach($data as $item) {
                    $csv_data .= ',' . $item;
                }
            }
            fclose($handle);
        }

        $domains = array();
        
        foreach(str_getcsv($csv_data) as $item) { 
            if (!StringHelper::ends_with($item, self::SUB_DOMAIN)) {
                continue;
            }
            if (!StringHelper::starts_with($item,'https') || !StringHelper::starts_with($item,'http')) {
                array_push($domains, 'https://'.$item);
            }
            else {
                array_push($domains, $item);
            }
        }
        
        return $domains;
    }
}