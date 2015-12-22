<?php
namespace Invoice\Domain;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $collection = $this->getMockBuilder('Invoice\\Domain\\Collection')
                     ->disableOriginalConstructor()
                     ->getMock();

        $collection->method('all')
             ->willReturn([]);

        $index = new Index($collection);
        $payload = $index([]);

        $this->assertArrayHasKey('success', $payload);
        $this->assertTrue($payload['success']);
        $this->assertArrayHasKey('invoices', $payload);
        $this->assertInternalType('array', $payload['invoices']);
        $this->assertCount(0, $payload['invoices']);
    }
}
