<?php

namespace Pina\Modules\Users;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;
use Pina\Hash;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());
        
        if (empty($moduleId)) {
            throw new \Exception('can not install module '.__NAMESPACE__);
            return;
        }
        
        self::createAdminUser();
    }
    
    public static function createAdminUser()
    {
        $adminExists = UserGateway::instance()->whereBy('group', 'root')->exists();
        if (empty($adminExists)) {
            $adminExists = UserGateway::instance()->whereBy('email', 'admin')->exists();
        }
        if (empty($adminExists)) {
            UserGateway::instance()->insert(array(
                'email' => 'admin',
                'password' => Hash::make('admin'),
                'group' => 'root',
                'status' => 'active'
            ));
        }
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
