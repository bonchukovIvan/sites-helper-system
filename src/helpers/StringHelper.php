<?php

namespace app\helpers;

class StringHelper {

    public static function ends_with($haystack, $needle): string {
        return substr($haystack, -strlen($needle)) === $needle;
    }
    
    public static  function starts_with($haystack, $needle): string  {
        return substr($haystack, strlen($needle)) === $needle;
    }
}