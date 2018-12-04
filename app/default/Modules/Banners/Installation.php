<?php

namespace Pina\Modules\Banners;

use Pina\InstallationInterface;
use Pina\Modules\CMS\ContentTypeGateway;
use Pina\Modules\CMS\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        self::createContentType();
    }

    public static function createContentType()
    {
        $data = array(
            'type' => 'banner-content',
            'title' => 'Баннеры',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }
    }

    public static function remove()
    {
        
    }

}
