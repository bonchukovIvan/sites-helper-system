<?php

use app\helpers\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase {
    public function test_ends_with(){
        self::assertTrue(true, StringHelper::ends_with('test.sumdu.edu.ua', '.edu.ua'));

        self::assertFalse(false, StringHelper::ends_with('test.sumdu.edu.ua', '.edu.ua1'));
        self::assertFalse(false, StringHelper::ends_with('test.sumdu.edu.ua', ''));
        self::assertFalse(false, StringHelper::ends_with('test.sumdu.edu.ua', null));
        self::assertFalse(false, StringHelper::ends_with('test.sumdu.edu.ua', 1));
        self::assertFalse(false, StringHelper::ends_with('test.sumdu.edu.ua', ['test', 'test2']));
    }
    
    public function test_starts_with() {
        self::assertTrue(StringHelper::starts_with('test.sumdu.edu.ua', 'test'));

        self::assertFalse(StringHelper::starts_with('test.sumdu.edu.ua', ''));
        self::assertFalse(StringHelper::starts_with('test.sumdu.edu.ua', '.edu.ua1'));
        self::assertFalse(StringHelper::starts_with('test.sumdu.edu.ua', null));
        self::assertFalse(StringHelper::starts_with('test.sumdu.edu.ua', 1));
        self::assertFalse(StringHelper::starts_with('test.sumdu.edu.ua', ['test', 'test2']));
    }
}