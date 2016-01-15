<?php declare(strict_types = 1);

namespace Invoice\Domain\Action;

class ViewSingleInvoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoiceFound()
    {
        $mapper = $this->getMockBuilder('Invoice\\Domain\\Mapper')
                     ->disableOriginalConstructor()
                     ->getMock();

        $mapper->method('byNumber')
             ->willReturn(['number' => 'sample']);

        $index = new ViewSingleInvoice($mapper);
        $payload = $index(['number' => 'sample']);

        $this->assertArrayHasKey('success', $payload);
        $this->assertTrue($payload['success']);
        $this->assertArrayHasKey('invoice', $payload);
        $this->assertInternalType('array', $payload['invoice']);
        $this->assertArrayHasKey('number', $payload['invoice']);
        $this->assertEquals('sample', $payload['invoice']['number']);
    }

    public function testInvoiceNotFound()
    {
        $mapper = $this->getMockBuilder('Invoice\\Domain\\Mapper')
                     ->disableOriginalConstructor()
                     ->getMock();

        $mapper->method('byNumber')
             ->willReturn([]);

        $index = new ViewSingleInvoice($mapper);
        $payload = $index(['number' => 'sample']);

        $this->assertArrayHasKey('success', $payload);
        $this->assertFalse($payload['success']);
        $this->assertArrayHasKey('message', $payload);
        $this->assertEquals('Invoice sample was not found.', $payload['message']);
    }
}
