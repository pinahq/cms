<?php

namespace Pina\Modules\MailChimp;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;
use Pina\Modules\CMS\ConfigGateway;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());
        
        if (empty($moduleId)) {
            throw new \Exception('can not install module '.__NAMESPACE__);
            return;
        }
        
        self::createConfig();
    }

    public static function createConfig()
    {
        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Настройки')->insertIgnore([
                [
                    'key' => 'api_key',
                    'title' => 'Api key',
                    'value' => '',
                    'order' => "1"
                ],
                [
                    'key' => 'list_id',
                    'title' => 'List id',
                    'value' => '',
                    'order' => "2"
                ]
            ]);
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
