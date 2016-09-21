<?php
declare(strict_types = 1);

namespace Invoice;

use Aura\Di\AbstractContainerConfigTest;

class ConfigTest extends AbstractContainerConfigTest
{
    protected function getConfigClasses()
    {
        return [
            'Cadre\Core\Config',
            'Invoice\Config',
        ];
    }

    public function provideGet()
    {
        return [
            ['invoice/domain:mapper', 'Invoice\Persistence\FilesystemMapper'],
        ];
    }

    public function provideNewInstance()
    {
        return [
            ['Invoice\Persistence\FilesystemMapper'],
            ['Invoice\Domain\Action\ListAllInvoices'],
            ['Invoice\Domain\Action\ViewSingleInvoice'],
        ];
    }
}
