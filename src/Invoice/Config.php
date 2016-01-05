<?php
namespace Invoice;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
    public function define(Container $di)
    {
        /**
         * Services
         */
        $di->set('puli:factory', $di->lazyNew(PULI_FACTORY_CLASS));
        $di->set('puli:repo', $di->lazyGetCall('puli:factory', 'createRepository'));
        $di->set('invoice/domain:mapper', $di->lazyNew('Invoice\Puli\Mapper'));
        $di->set('twig', $di->lazyNew('Twig_Environment'));

        /**
         * Invoice Mapper
         */
        $di->params['Invoice\Puli\Mapper']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Invoice\Puli\Mapper']['yaml'] = $di->lazyNew('Symfony\Component\Yaml\Parser');

        /**
         * Twig_Environment
         */
        $di->params['Puli\TwigExtension\PuliTemplateLoader']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Puli\TwigExtension\PuliExtension']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Twig_Environment']['loader'] = $di->lazyNew('Puli\TwigExtension\PuliTemplateLoader');
        $di->params['Twig_Environment']['options'] = ['debug' => true, 'strict_variables' => true];

        /**
         * ListAllInvoices
         */
        $di->params['Invoice\Domain\Action\ListAllInvoices']['mapper'] = $di->lazyGet('invoice/domain:mapper');

        /**
         * ViewSingleInvoice
         */
        $di->params['Invoice\Domain\Action\ViewSingleInvoice']['mapper'] = $di->lazyGet('invoice/domain:mapper');

        /**
         * Responder
         */
        $di->params['Invoice\Responder']['twig'] = $di->lazyGet('twig');
    }

    public function modify(Container $di)
    {
        $twig = $di->get('twig');
        $twig->addExtension($di->newInstance('Twig_Extension_Debug'));
        $twig->addExtension($di->newInstance('Puli\TwigExtension\PuliExtension'));
    }
}
