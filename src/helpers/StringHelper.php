<?php

namespace app\helpers;

class StringHelper {

    public static function ends_with($haystack, $needle): bool {
        if ($needle === '' || !is_string($needle)) {
            return false;
        }
        return substr($haystack, -strlen($needle)) === $needle;
    }
    
    public static  function starts_with($haystack, $needle): bool  {
        if ($needle === '' || !is_string($needle)) {
            return false;
        }
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}