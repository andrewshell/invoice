<?php declare(strict_types = 1);

namespace Invoice\Domain\Action;

class ListAllInvoicesTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $mapper = $this->getMockBuilder('Invoice\\Domain\\Mapper')
                     ->disableOriginalConstructor()
                     ->getMock();

        $mapper->method('all')
             ->willReturn([]);

        $index = new ListAllInvoices($mapper);
        $payload = $index([]);

        $this->assertArrayHasKey('success', $payload);
        $this->assertTrue($payload['success']);
        $this->assertArrayHasKey('invoices', $payload);
        $this->assertInternalType('array', $payload['invoices']);
        $this->assertCount(0, $payload['invoices']);
    }
}
