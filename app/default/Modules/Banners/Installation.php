<?php

namespace Pina\Modules\Banners;

use Pina\Modules\CMS\ContentTypeGateway;
use Pina\InstallationInterface;
use Pina\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());

        if (empty($moduleId)) {
            throw new \Exception('can not install module ' . __NAMESPACE__);
            return;
        }

        self::createContentType($moduleId);
    }

    public static function createContentType($moduleId)
    {
        $data = array(
            'module_id' => $moduleId,
            'type' => 'banner-content',
            'title' => 'Баннеры',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
