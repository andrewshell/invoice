<?php
namespace Invoice\Domain\Action;

use Invoice\Domain\Mapper;
use Symfony\Component\Yaml\Parser;
use Twig_Environment;

class ListAllInvoices
{
    protected $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke()
    {
        return [
            'success' => true,
            'invoices' => $this->mapper->all(),
        ];
    }
}
