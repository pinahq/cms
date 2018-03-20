<?php

namespace Pina\Modules\reCAPTCHA;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;
use Pina\Modules\CMS\ConfigGateway;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());

        if (empty($moduleId)) {
            throw new \Exception('can not install module ' . __NAMESPACE__);
            return;
        }

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
        #echo 'remove';
    }

}
