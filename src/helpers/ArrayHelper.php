<?php

namespace app\helpers;

use UnexpectedValueException;

class ArrayHelper {
    private static function array_filter($array) {
        if (empty($array)) {
            return [];
        }
        if(!is_array($array)) {
            throw new UnexpectedValueException('Unexpected value type in params, domains must be array');
        }
    } 
    public static function create_suspected_array($http_array) { 
        self::array_filter($http_array);

        $suspected_array = array(); 
        
        foreach($http_array as $domain => $http_code) { 
            if ($http_code === 500) {
                $suspected_array[$domain] = $http_code; 
            } 
        } 

        return $suspected_array; 
    } 
    
    public static function create_updated_array($http_array) { 
        self::array_filter($http_array);

        $updated_array = array(); 
        
        foreach ($http_array as $domain => $http_code) { 
            if ($http_code < 500) { 
                $updated_array[$domain] = $http_code; 
            } 
        } 

        return $updated_array; 
    } 
}