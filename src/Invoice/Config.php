<?php
namespace Invoice;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
    public function define(Container $di)
    {
        /**
         * Values
         */
        $invoicePath = __DIR__ . '/../../invoices';
        $viewPath = __DIR__ . '/Resources/views';

        /**
         * Invoice Collection
         */
        $di->params['Invoice\Domain\Collection']['path'] = $invoicePath;
        $di->params['Invoice\Domain\Collection']['yaml'] = $di->lazyNew('Symfony\Component\Yaml\Parser');

        /**
         * Twig_Environment
         */
        $di->params['Twig_Loader_Filesystem']['paths'] = [$viewPath];
        $di->params['Twig_Environment']['loader'] = $di->lazyNew('Twig_Loader_Filesystem');
        $di->params['Twig_Environment']['options'] = ['debug' => true, 'strict_variables' => true];
        $di->setters['Twig_Environment']['addExtension'] = $di->lazyNew('Twig_Extension_Debug');

        /**
         * Index
         */
        $di->params['Invoice\Domain\Index']['invoices'] = $di->lazyNew('Invoice\Domain\Collection');

        /**
         * Invoice
         */
        $di->params['Invoice\Domain\Invoice']['invoices'] = $di->lazyNew('Invoice\Domain\Collection');

        /**
         * Responder
         */
        $di->params['Invoice\Responder']['twig'] = $di->lazyNew('Twig_Environment');
    }

    public function modify(Container $di)
    {

    }
}
