<?php declare(strict_types = 1);

namespace Cadre\Core;

use Aura\Di\AbstractContainerConfigTest;

class ConfigTest extends AbstractContainerConfigTest
{
    protected function getConfigClasses()
    {
        return [
            'Cadre\Core\Config',
        ];
    }

    public function provideGet()
    {
        return [
            ['puli:factory', PULI_FACTORY_CLASS],
            ['puli:repo', 'Puli\Repository\Api\ResourceRepository'],
            ['twig', 'Twig_Environment'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Cadre\Core\Responder\TwigResponder'],
            ['Puli\TwigExtension\PuliExtension'],
            ['Puli\TwigExtension\PuliTemplateLoader'],
            ['Twig_Environment'],
        ];
    }
}
