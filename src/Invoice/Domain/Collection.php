<?php
namespace Invoice\Domain;

use Symfony\Component\Yaml\Parser;
use Twig_Environment;

class Collection
{
    protected $path;
    protected $yaml;

    public function __construct($path, Parser $yaml)
    {
        $this->path = $path;
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
        $invoiceFiles = scandir($this->path);
        foreach ($invoiceFiles as $filename) {
            if ('_' == $filename[0]) {
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

    protected function readInvoice($filename)
    {
        $fullPath = $this->path . '/' . $filename;
        if (file_exists($fullPath) && preg_match('!\.yml$!', $fullPath)) {
            $invoice = $this->yaml->parse(file_get_contents($fullPath));
            if (!is_array($invoice)) {
                $invoice = [];
            }
            $invoice = array_merge($this->readGlobalData(), $invoice);
            $invoice['subtotal'] = 0;
            if (empty($invoice['number'])) {
                $invoice['number'] = basename($filename, '.yml');
            }
            if (empty($invoice['date'])) {
                $invoice['date'] = filemtime($fullPath);
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
                    if (empty($invoice['items'][$i]['desc'])) {
                        $invoice['items'][$i]['desc'] = 'Unknown Item';
                    }
                    if (empty($invoice['items'][$i]['unit_cost'])) {
                        $invoice['items'][$i]['unit_cost'] = 0;
                    }
                    if (empty($invoice['items'][$i]['quantity'])) {
                        $invoice['items'][$i]['quantity'] = 1;
                    }
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

    function readGlobalData()
    {
        $filename = '_global.yml';
        $fullPath = $this->path . '/' . $filename;
        if (file_exists($fullPath)) {
            $globalData = $this->yaml->parse(file_get_contents($fullPath));
        }
        if (empty($globalData)) {
            $globalData = array();
        }

        return $globalData;
    }

    function dateDecrementingSort($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] > $b['date']) ? -1 : 1;
    }
}
