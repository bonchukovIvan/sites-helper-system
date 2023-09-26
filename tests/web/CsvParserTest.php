<?php

use app\web\CsvParser;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase {
    public function test_get_domains_from_csv() {
        $tempCsvFile = tempnam(sys_get_temp_dir(), 'test_csv');
        file_put_contents($tempCsvFile, "domains,http://example.com,https://example.edu.ua\nhttp://google.com,https://yahoo.edu.ua");

        $domains = CsvParser::get_domains_from_csv($tempCsvFile);
        $expectedDomains = ['https://example.edu.ua','https://yahoo.edu.ua'];
        $this->assertEquals($expectedDomains, $domains);

        unlink($tempCsvFile);
    }
}