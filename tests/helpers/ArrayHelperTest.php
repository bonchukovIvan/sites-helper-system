<?php

use app\helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase {
    const INPUT_ARRAY = [
        'test.domain' => 500,
        'test1.domain' => 200,
        'test2.domain' => 503,
        'test3.domain' => 500,
        'test4.domain' => 301,
        'test5.domain' => 500,
        'test6.domain' => 500,
    ];
    const EXPECTED_ARRAY = [
        'test.domain' => 500,
        'test2.domain' => 503,
        'test3.domain' => 500,
        'test5.domain' => 500,
        'test6.domain' => 500,
    ];

    public function test_create_suspected_array() {
        self::assertEquals(self::EXPECTED_ARRAY, ArrayHelper::create_suspected_array(self::INPUT_ARRAY));
        self::assertEquals([], ArrayHelper::create_suspected_array([]));
        
        self::expectException(UnexpectedValueException::class);
        ArrayHelper::create_suspected_array(null);
        ArrayHelper::create_suspected_array(1);
    }
}