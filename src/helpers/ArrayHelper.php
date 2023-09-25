<?php

namespace app\helpers;

use UnexpectedValueException;

class ArrayHelper {
    public static function create_suspected_array($http_array) { 
        if(!is_array($http_array)) {
            throw new UnexpectedValueException('Unexpected value type in params, domains must be array');
        }

        $suspected_array = array(); 
        
        foreach($http_array as $domain => $http_code) { 
            if($http_code >= 500) { 
                $suspected_array[$domain] = $http_code; 
            } 
        } 

        return $suspected_array; 
    } 
}