<?php declare(strict_types = 1);

namespace Invoice;

use Aura\Di\AbstractContainerConfigTest;

class ConfigTest extends AbstractContainerConfigTest
{
    protected function getConfigClasses()
    {
        return [
            'Invoice\Config',
        ];
    }

    public function provideGet()
    {
        return [
            ['puli:factory', PULI_FACTORY_CLASS],
            ['puli:repo', 'Puli\Repository\Api\ResourceRepository'],
            ['invoice/domain:mapper', 'Invoice\Puli\Mapper'],
            ['twig', 'Twig_Environment'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Invoice\Puli\Mapper'],
            ['Invoice\Domain\Action\ListAllInvoices'],
            ['Invoice\Domain\Action\ViewSingleInvoice'],
            ['Invoice\Responder'],
            ['Puli\TwigExtension\PuliExtension'],
            ['Puli\TwigExtension\PuliTemplateLoader'],
            ['Twig_Environment'],
        ];
    }
}
