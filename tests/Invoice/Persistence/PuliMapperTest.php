<?php
declare(strict_types = 1);

namespace Invoice\Persistence;

use Invoice\Domain\Normalizer;
use Invoice\Persistence\Puli\TestFile;
use Puli\Repository\InMemoryRepository;
use Symfony\Component\Yaml\Parser;

class PuliMapperTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;
    protected $mapper;

    public function setUp()
    {
        $this->repo = new InMemoryRepository();
        $yaml = new Parser();
        $normalizer = new Normalizer();
        $this->mapper = new PuliMapper($this->repo, $yaml, $normalizer);
    }

    public function testEmptyDirectory()
    {
        $invoices = $this->mapper->all();

        $this->assertInternalType('array', $invoices);
        $this->assertEmpty($invoices);
    }

    public function testSingleEmptyFileWithoutGlobal()
    {
        $this->repo->add('/app/invoices/sample.yml', new TestFile('/sample.yml', ''));

        $invoices = $this->mapper->all();

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
        $this->repo->add('/app/invoices/_global.yml', new TestFile('/_global.yml', 'date: "2015-12-22"'));
        $this->repo->add('/app/invoices/sample.yml', new TestFile('/sample.yml', ''));

        $invoices = $this->mapper->all();

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
        $this->repo->add('/app/invoices/sample1.yml', new TestFile('/sample1.yml', 'date: "2015-12-22"'));
        $this->repo->add('/app/invoices/sample2.yml', new TestFile('/sample2.yml', 'date: "2015-12-21"'));
        $this->repo->add('/app/invoices/sample3.yml', new TestFile('/sample3.yml', 'date: "2015-12-22"'));

        $invoices = $this->mapper->all();

        $this->assertInternalType('array', $invoices);
        $this->assertCount(3, $invoices);
        $this->assertArrayHasKey('sample1', $invoices);
        $this->assertArrayHasKey('sample2', $invoices);
        $this->assertArrayHasKey('sample3', $invoices);
        $this->assertEquals('sample2', array_keys($invoices)[2]);
    }

    public function testSingleFileByNumber()
    {
        $this->repo->add(
            '/app/invoices/sample.yml',
            new TestFile(
                '/sample.yml',
                "number: inv-123\n" .
                "date: 2015-12-22\n" .
                "items:\n" .
                "    - other: value\n"
            )
        );

        $invoice = $this->mapper->byNumber('inv-123');

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
        $invoice = $this->mapper->byNumber('missing');

        $this->assertEmpty($invoice);
    }
}
