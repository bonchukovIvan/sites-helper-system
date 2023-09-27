<?php

use app\log\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {

    public function testInit() {
        $logger = new Logger();

        $this->assertTrue($logger->init());
        $this->assertDirectoryExists(dirname(__DIR__, 2) . Logger::LOGS_DIR);
    }

    public function testWriteDomainsToLog() {
        $logger = new Logger();

        $files_count = count(scandir(dirname(__DIR__, 2) . Logger::LOGS_DIR))-2;

        $domains = ['example.com' => 200, 'google.com' => 500];
        $result = $logger->write_domains_to_log($domains);
        $this->assertTrue($result);

        $final_files_count = count(scandir(dirname(__DIR__, 2) . Logger::LOGS_DIR))-2;
        $this->assertEquals($final_files_count, $files_count+1);

        $this->expectException(UnexpectedValueException::class);
        $logger->write_domains_to_log('not_an_array');
    }

    public function testGetLastLog() {
        $logger = new Logger();
        $logContent = $logger->get_last_log();

        $this->assertIsBool($logContent);
    }

}