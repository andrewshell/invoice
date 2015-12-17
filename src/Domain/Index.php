<?php
namespace Invoice\Domain;

use Symfony\Component\Yaml\Parser;
use Twig_Environment;

class Index
{
    protected $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function __invoke(array $input)
    {
        return [
            'success' => true,
            'invoices' => $this->invoices->all(),
        ];
    }
}
