<?php
declare(strict_types = 1);

namespace Cadre\Core;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
    public function define(Container $di)
    {
        /**
         * Services
         */
        $di->set('cadre/core:twig_responder', $di->lazyNew('Cadre\Core\Responder\TwigResponder'));
        $di->set('puli:factory', $di->lazyNew(PULI_FACTORY_CLASS));
        $di->set('puli:repo', $di->lazyGetCall('puli:factory', 'createRepository'));
        $di->set('twig', $di->lazyNew('Twig_Environment'));

        /**
         * Twig_Environment
         */
        $di->params['Puli\TwigExtension\PuliTemplateLoader']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Puli\TwigExtension\PuliExtension']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Twig_Environment']['loader'] = $di->lazyNew('Puli\TwigExtension\PuliTemplateLoader');
        $di->params['Twig_Environment']['options'] = ['debug' => true, 'strict_variables' => true];

        /**
         * Responder
         */
        $di->params['Cadre\Core\Responder\TwigResponder']['twig'] = $di->lazyGet('twig');
    }

    public function modify(Container $di)
    {

    }
}
