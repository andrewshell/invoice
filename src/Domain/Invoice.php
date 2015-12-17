<?php
namespace Invoice\Domain;

use Symfony\Component\Yaml\Parser;
use Twig_Environment;

class Invoice
{
    protected $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function __invoke(array $input)
    {
        $invoice = $this->invoices->byNumber($input['number']);
        if (is_null($invoice)) {
            return [
                'success' => false,
                'message' => sprintf('Invoice %s was not found.', $input['number']),
            ];
        } else {
            return [
                'success' => true,
                'invoice' => $invoice,
            ];
        }
    }
}
