<?php
declare(strict_types = 1);

namespace Invoice\Persistence;

use Invoice\Domain\Mapper as DomainMapper;
use Invoice\Domain\Normalizer;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Api\Resource\BodyResource;
use Symfony\Component\Yaml\Parser;

class PuliMapper implements DomainMapper
{
    protected $repo;
    protected $yaml;
    protected $normalizer;

    public function __construct(ResourceRepository $repo, Parser $yaml, Normalizer $normalizer)
    {
        $this->repo = $repo;
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
        foreach ($this->repo->find('/app/invoices/*.yml') as $resource) {
            $filename = $resource->getName();
            if ('_' == $filename[0]) {
                continue;
            }
            $invoice = $this->readInvoice($resource);
            if (is_array($invoice)) {
                $invoices[$invoice['number']] = $invoice;
            }
        }
        uasort($invoices, [$this, 'dateDecrementingSort']);
        return $invoices;
    }

    protected function readInvoice(BodyResource $resource): array
    {
        $invoice = $this->yaml->parse($resource->getBody());
        if (!is_array($invoice)) {
            $invoice = [];
        }
        $invoice = array_merge($this->readGlobalData(), $invoice);
        return $this->normalizer->normalize($invoice, basename($resource->getName(), '.yml'));
    }

    protected function readGlobalData(): array
    {
        if ($this->repo->contains('/app/invoices/_global.yml')) {
            $resource = $this->repo->get('/app/invoices/_global.yml');
            $globalData = $this->yaml->parse($resource->getBody());
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
