<?php

namespace Pina\Modules\reCAPTCHA;

use Pina\InstallationInterface;
use Pina\Modules\CMS\ModuleRegistry;
use Pina\Modules\CMS\ConfigGateway;

class Installation implements InstallationInterface
{

    public static function install()
    {
        self::createConfig();
    }

    public static function createConfig()
    {
        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Настройки')->insertIgnore([
                [
                'key' => 'site_key',
                'title' => 'Site key',
                'type' => 'text',
                'value' => '',
                'order' => "0"
            ],
                [
                'key' => 'secret_key',
                'title' => 'Secret key',
                'type' => 'text',
                'value' => '',
                'order' => "1"
            ],
        ]);
    }

    public static function remove()
    {
        
    }

}
