<?php
declare(strict_types = 1);

namespace Invoice\Persistence;

use Invoice\Domain\Mapper as DomainMapper;
use Invoice\Domain\Normalizer;
use Symfony\Component\Yaml\Parser;

class FilesystemMapper implements DomainMapper
{
    protected $path;
    protected $yaml;
    protected $normalizer;

    public function __construct($path, Parser $yaml, Normalizer $normalizer)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->yaml = $yaml;
        $this->normalizer = $normalizer;
    }

    public function all(): array
    {
        return $this->readInvoices();
    }

    public function byNumber(string $number): array
    {
        $invoices = $this->readInvoices();
        if (isset($invoices[$number])) {
            return $invoices[$number];
        } else {
            return array();
        }
    }

    protected function readInvoices(): array
    {
        $invoices = array();
        $yamlMatch = $this->path . DIRECTORY_SEPARATOR . '*.yml';
        foreach (glob($yamlMatch) as $filename) {
            if ('_' == basename($filename)[0]) {
                continue;
            }
            $invoice = $this->readInvoice($filename);
            if (is_array($invoice)) {
                $invoices[$invoice['number']] = $invoice;
            }
        }
        uasort($invoices, [$this, 'dateDecrementingSort']);
        return $invoices;
    }

    protected function readInvoice($filename): array
    {
        $invoice = $this->yaml->parse(file_get_contents($filename));
        if (!is_array($invoice)) {
            $invoice = [];
        }
        $invoice = array_merge($this->readGlobalData(), $invoice);
        return $this->normalizer->normalize($invoice, basename($filename, '.yml'));
    }

    protected function readGlobalData(): array
    {
        $filename = $this->path . DIRECTORY_SEPARATOR . '_global.yml';
        if (file_exists($filename)) {
            $globalData = $this->yaml->parse(file_get_contents($filename));
        }
        if (empty($globalData)) {
            $globalData = array();
        }

        return $globalData;
    }

    protected function dateDecrementingSort(array $a, array $b): int
    {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] > $b['date']) ? -1 : 1;
    }
}
