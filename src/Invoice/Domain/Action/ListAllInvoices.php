<?php
declare(strict_types = 1);

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

    public function __invoke(): array
    {
        return [
            'success' => true,
            'invoices' => $this->mapper->all(),
        ];
    }
}
