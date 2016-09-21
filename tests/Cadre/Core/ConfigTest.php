<?php
declare(strict_types = 1);

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
            ['cadre/core:twig_responder', 'Cadre\Core\Responder\TwigResponder'],
            ['twig', 'Twig_Environment'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Cadre\Core\Responder\TwigResponder'],
            ['Twig_Loader_Filesystem'],
            ['Twig_Environment'],
        ];
    }
}
