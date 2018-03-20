<?php

namespace Pina\Modules\YandexKassa;

use Pina\ModuleInterface;

class Module implements ModuleInterface
{

    public function getPath()
    {
        return __DIR__;
    }
    
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'Yandex Kassa';
    }

    public function http()
    {
        return [
            'yandex-kassa',
            'yandex-kassa-check-order',
            'yandex-kassa-payment-aviso',
        ];
    }
    
    public function cli()
    {
        
    }
    
    public function boot()
    {
        
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
