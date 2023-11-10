<?php 

namespace app\web;

use app\helpers\ArrayHelper;
use UnexpectedValueException;

class DomainsChecker {

    public static function create_domain_list($domains_array) {
        $http_array = self::check_domains($domains_array); 
    
        $suspected_domains = ArrayHelper::create_suspected_array($http_array);
        if(!$suspected_domains) {
            return $http_array;
        } else {
            $suspected_domains = array_keys($suspected_domains);
            $new_http_array = self::check_domains($suspected_domains); 
            $suspected_domains = ArrayHelper::create_suspected_array($new_http_array);
    
            $http_array = array_merge($http_array, $new_http_array);
            return $http_array;
        }
    }

    public static function check_domains($domains) {
        if(!is_array($domains)) {
            throw new UnexpectedValueException('Unexpected value type in params, domains must be array');
        }

        $handles = self::get_handles($domains);

        $mh = curl_multi_init();
        self::add_handles_to_multi($mh, $handles);
    
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while($running > 0);

        self::close_handle($mh, $handles);
        return self::get_http_codes($handles);
    } 
    
    private static function get_handles($domains) {
        $handles = array();
        foreach($domains as $domain) {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $domain);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_VERBOSE, false);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
                curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }

            $handles[$domain] = $handle;
        }

        return $handles;
    }
    private static function add_handles_to_multi($mh, $handles) {
        foreach($handles as $handle) {
            curl_multi_add_handle($mh, $handle);
        }
    }

    private static function get_http_codes($handles) {
        $http_array = array();
        foreach($handles as $domain => $handle) {
            $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            if (!$http_code) {
                $http_code = 500;
            }
            $http_array[$domain] = $http_code;
        }
        return $http_array;
    }
    
    private static function close_handle($mh, $handles) {
        foreach($handles as $handle) {
            curl_multi_remove_handle($mh, $handle);
        }
        curl_multi_close($mh);
    }
}