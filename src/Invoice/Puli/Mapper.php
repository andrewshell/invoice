<?php
namespace Invoice\Puli;

use Invoice\Domain\Mapper as DomainMapper;
use Puli\Repository\Api\ResourceRepository;
use Symfony\Component\Yaml\Parser;

class Mapper implements DomainMapper
{
    protected $repo;
    protected $yaml;

    public function __construct(ResourceRepository $repo, Parser $yaml)
    {
        $this->repo = $repo;
        $this->yaml = $yaml;
    }

    public function all()
    {
        return $this->readInvoices();
    }

    public function byNumber($number)
    {
        $invoices = $this->readInvoices();
        if (isset($invoices[$number])) {
            return $invoices[$number];
        } else {
            return null;
        }
    }

    protected function readInvoices()
    {
        $invoices = array();
        foreach ($this->repo->find('/app/invoices/*.yml') as $resource) {
            $filename = $resource->getName();
            if ('_' == $filename[0]) {
                continue;
            }
            $invoice = $this->readInvoice($resource->getFilesystemPath());
            if (is_array($invoice)) {
                $invoices[$invoice['number']] = $invoice;
            }
        }
        uasort($invoices, [$this, 'dateDecrementingSort']);
        return $invoices;
    }

    protected function readInvoice($path)
    {
        $filename = basename($path);
        if (file_exists($path) && preg_match('!\.yml$!', $path)) {
            $invoice = $this->yaml->parse(file_get_contents($path));
            if (!is_array($invoice)) {
                $invoice = [];
            }
            $invoice = array_merge($this->readGlobalData(), $invoice);
            $invoice['subtotal'] = 0;
            if (empty($invoice['number'])) {
                $invoice['number'] = basename($filename, '.yml');
            }
            if (empty($invoice['date'])) {
                $invoice['date'] = filemtime($path);
            } elseif (is_string($invoice['date'])) {
                $invoice['date'] = strtotime($invoice['date']);
            }
            if (empty($invoice['paid'])) {
                $invoice['paid'] = 0;
            }
            if (empty($invoice['items'])) {
                $invoice['items'] = array();
            }
            if (is_array($invoice['items'])) {
                foreach (array_keys($invoice['items']) as $i) {
                    $invoice['items'][$i] = array_merge(
                        [
                            'desc' => 'Unknown Item',
                            'unit_cost' => 0,
                            'quantity' => 1,
                        ],
                        $invoice['items'][$i]
                    );
                    $invoice['items'][$i]['price'] = ($invoice['items'][$i]['unit_cost'] * $invoice['items'][$i]['quantity']);
                    $invoice['subtotal'] += $invoice['items'][$i]['price'];
                }
            }
            $invoice['total'] = $invoice['subtotal'] - $invoice['paid'];
        } else {
            $invoice = null;
        }
        return $invoice;
    }

    protected function readGlobalData()
    {
        if ($this->repo->contains('/app/invoices/_global.yml')) {
            $contents = $this->repo->get('/app/invoices/_global.yml')->getBody();
            $globalData = $this->yaml->parse($contents);
        }
        if (empty($globalData)) {
            $globalData = array();
        }

        return $globalData;
    }

    protected function dateDecrementingSort($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] > $b['date']) ? -1 : 1;
    }
}
