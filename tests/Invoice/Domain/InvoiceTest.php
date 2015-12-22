<?php
namespace Invoice\Domain;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoiceFound()
    {
        $collection = $this->getMockBuilder('Invoice\\Domain\\Collection')
                     ->disableOriginalConstructor()
                     ->getMock();

        $collection->method('byNumber')
             ->willReturn(['number' => 'sample']);

        $index = new Invoice($collection);
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
        $collection = $this->getMockBuilder('Invoice\\Domain\\Collection')
                     ->disableOriginalConstructor()
                     ->getMock();

        $collection->method('byNumber')
             ->willReturn(null);

        $index = new Invoice($collection);
        $payload = $index(['number' => 'sample']);

        $this->assertArrayHasKey('success', $payload);
        $this->assertFalse($payload['success']);
        $this->assertArrayHasKey('message', $payload);
        $this->assertEquals('Invoice sample was not found.', $payload['message']);
    }
}
