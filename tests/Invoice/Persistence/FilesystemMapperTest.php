<?php
declare(strict_types = 1);

namespace Invoice\Persistence;

use Invoice\Domain\Normalizer;
use Invoice\Persistence\Puli\TestFile;
use Puli\Repository\InMemoryRepository;
use Symfony\Component\Yaml\Parser;

class FilesystemMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyDirectory()
    {
        $path = __DIR__ . '/FilesystemMapper/empty-dir';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoices = $mapper->all();

        $this->assertInternalType('array', $invoices);
        $this->assertEmpty($invoices);
    }

    public function testSingleEmptyFileWithoutGlobal()
    {
        $path = __DIR__ . '/FilesystemMapper/empty-file';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoices = $mapper->all();

        $defaults = array(
            'subtotal' => 0,
            'number' => 'sample',
            'date' => 0,
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
        $path = __DIR__ . '/FilesystemMapper/empty-w-global';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoices = $mapper->all();

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
        $path = __DIR__ . '/FilesystemMapper/order-dates';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoices = $mapper->all();

        $this->assertInternalType('array', $invoices);
        $this->assertCount(3, $invoices);
        $this->assertArrayHasKey('sample1', $invoices);
        $this->assertArrayHasKey('sample2', $invoices);
        $this->assertArrayHasKey('sample3', $invoices);
        $this->assertEquals('sample2', array_keys($invoices)[2]);
    }

    public function testSingleFileByNumber()
    {
        $path = __DIR__ . '/FilesystemMapper/by-number';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoice = $mapper->byNumber('inv-123');

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
        $path = __DIR__ . '/FilesystemMapper/by-number';
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $mapper = new FilesystemMapper($path, $yaml, $normalizer);

        $invoice = $mapper->byNumber('missing');

        $this->assertEmpty($invoice);
    }
}
