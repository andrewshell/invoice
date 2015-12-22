<?php
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

    public function provideNewInstance()
    {
        return [
            ['Invoice\Domain\Collection'],
            ['Invoice\Domain\Index'],
            ['Invoice\Domain\Invoice'],
            ['Invoice\Responder'],
            ['Twig_Loader_Filesystem'],
            ['Twig_Environment'],
        ];
    }
}
