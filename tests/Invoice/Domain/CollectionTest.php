<?php
namespace Invoice\Domain;

use Symfony\Component\Yaml\Parser;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStream::setup('invoices');
        $yaml = new Parser();
        $this->invoicePath = vfsStream::url('invoices');
        $this->collection = new Collection($this->invoicePath, $yaml);
    }

    public function testEmptyDirectory()
    {
        $invoices = $this->collection->all();

        $this->assertInternalType('array', $invoices);
        $this->assertEmpty($invoices);
    }

    public function testSingleEmptyFileWithoutGlobal()
    {
        file_put_contents($this->invoicePath . '/sample.yml', '');

        $invoices = $this->collection->all();

        $defaults = array(
            'subtotal' => 0,
            'number' => 'sample',
            'date' => filemtime($this->invoicePath . '/sample.yml'),
            'paid' => 0,
            'items' => [],
            'total' => 0,
        );

        $this->assertInternalType('array', $invoices);
        $this->assertCount(1, $invoices);
        $this->assertArrayHasKey('sample', $invoices);

        foreach ($defaults as $k => $v) {
            $this->assertArrayHasKey($k, $invoices['sample']);
            $this->assertInternalType(gettype($v), $invoices['sample'][$k]);
            $this->assertEquals($v, $invoices['sample'][$k]);
        }
    }

    public function testSingleEmptyFileWithGlobal()
    {
        file_put_contents($this->invoicePath . '/_global.yml', 'date: "2015-12-22"');
        file_put_contents($this->invoicePath . '/sample.yml', '');

        $invoices = $this->collection->all();

        $defaults = array(
            'subtotal' => 0,
            'number' => 'sample',
            'date' => strtotime('2015-12-22'),
            'paid' => 0,
            'items' => [],
            'total' => 0,
        );

        $this->assertInternalType('array', $invoices);
        $this->assertCount(1, $invoices);
        $this->assertArrayHasKey('sample', $invoices);

        foreach ($defaults as $k => $v) {
            $this->assertArrayHasKey($k, $invoices['sample']);
            $this->assertInternalType(gettype($v), $invoices['sample'][$k]);
            $this->assertEquals($v, $invoices['sample'][$k]);
        }
    }

    public function testMultipleFilesOrderByDate()
    {
        file_put_contents($this->invoicePath . '/sample1.yml', 'date: "2015-12-22"');
        file_put_contents($this->invoicePath . '/sample2.yml', 'date: "2015-12-21"');
        file_put_contents($this->invoicePath . '/sample3.yml', 'date: "2015-12-22"');

        $invoices = $this->collection->all();

        $this->assertInternalType('array', $invoices);
        $this->assertCount(3, $invoices);
        $this->assertArrayHasKey('sample1', $invoices);
        $this->assertArrayHasKey('sample2', $invoices);
        $this->assertArrayHasKey('sample3', $invoices);
        $this->assertEquals('sample2', array_keys($invoices)[2]);
    }

    public function testSingleFileByNumber()
    {
        file_put_contents(
            $this->invoicePath . '/sample.yml',
            "number: inv-123\n" .
            "date: 2015-12-22\n" .
            "items:\n" .
            "    - other: value\n"
        );

        $invoice = $this->collection->byNumber('inv-123');

        $defaults = array(
            'other' => 'value',
            'desc' => 'Unknown Item',
            'unit_cost' => 0,
            'quantity' => 1,
            'price' => 0,
        );

        $this->assertInternalType('array', $invoice);
        $this->assertInternalType('array', $invoice['items']);
        $this->assertCount(1, $invoice['items']);

        foreach ($defaults as $k => $v) {
            $this->assertArrayHasKey($k, $invoice['items'][0]);
            $this->assertInternalType(gettype($v), $invoice['items'][0][$k]);
            $this->assertEquals($v, $invoice['items'][0][$k]);
        }
    }

    public function testMissingFileByNumber()
    {
        $invoice = $this->collection->byNumber('missing');

        $this->assertNull($invoice);
    }
}
