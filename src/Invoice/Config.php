<?php declare(strict_types = 1);

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
        $di->set('invoice/domain:mapper', $di->lazyNew('Invoice\Persistence\PuliMapper'));

        /**
         * Invoice Mapper
         */
        $di->params['Invoice\Persistence\PuliMapper']['repo'] = $di->lazyGet('puli:repo');
        $di->params['Invoice\Persistence\PuliMapper']['yaml'] = $di->lazyNew('Symfony\Component\Yaml\Parser');
        $di->params['Invoice\Persistence\PuliMapper']['normalizer'] = $di->lazyNew('Invoice\Domain\Normalizer');

        /**
         * ListAllInvoices
         */
        $di->params['Invoice\Domain\Action\ListAllInvoices']['mapper'] = $di->lazyGet('invoice/domain:mapper');

        /**
         * ViewSingleInvoice
         */
        $di->params['Invoice\Domain\Action\ViewSingleInvoice']['mapper'] = $di->lazyGet('invoice/domain:mapper');
    }

    public function modify(Container $di)
    {
        $twig = $di->get('twig');
        $twig->addExtension($di->newInstance('Twig_Extension_Debug'));
        $twig->addExtension($di->newInstance('Puli\TwigExtension\PuliExtension'));
    }
}
