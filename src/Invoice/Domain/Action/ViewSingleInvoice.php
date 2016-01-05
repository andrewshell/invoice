<?php
namespace Invoice\Domain\Action;

use Invoice\Domain\Mapper;
use Symfony\Component\Yaml\Parser;
use Twig_Environment;

class ViewSingleInvoice
{
    protected $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke(array $input)
    {
        $invoice = $this->mapper->byNumber($input['number']);
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
