<?php

use app\web\DomainsChecker;
use PHPUnit\Framework\TestCase;
use app\helpers\ArrayHelper;

class DomainsCheckerTest extends TestCase {

        public function testCreateDomainList() {

            $domainsArray = ['http://example.com', 'http://google.com'];
    
            $result = DomainsChecker::create_domain_list($domainsArray);
    
            $this->assertTrue(is_array($result));
            $this->assertNotEmpty($result);
        }
    
        public function testCreateDomainListWithSuspectedDomains() {
            $domainsArray = ['https://example.com', 'https://google.com'];
            $suspectedDomains = ['https://malicious.com'];
    
            ArrayHelper::create_suspected_array($suspectedDomains);
    
            $result = DomainsChecker::create_domain_list($domainsArray);

            $this->assertTrue(is_array($result));
            $this->assertNotEmpty($result);
        }
    
        public function testCreateDomainListWithInvalidInput() {
            $this->expectException(UnexpectedValueException::class);
            DomainsChecker::create_domain_list('invalid_input');
        }
}