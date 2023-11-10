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
            $clear_item = trim($item);

            if (!StringHelper::ends_with(trim($clear_item), self::SUB_DOMAIN)) {
                continue;
            }
            if (!StringHelper::starts_with($clear_item,'https') || !StringHelper::starts_with($clear_item,'http')) {
                    array_push($domains, 'https://'.$clear_item);
            }
            else {
                    array_push($domains, $clear_item);
            }
        }

        return $domains;
    }
}
