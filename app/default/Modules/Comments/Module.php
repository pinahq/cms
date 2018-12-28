<?php

namespace Pina\Modules\Comments;

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
        return 'Comments';
    }

    public function http()
    {
        \Pina\Composer::placeModule('page.bottom', 'resources/:resource_id/comments');
        
        return [
            'resources/comments',
        ];
    }

    public function cli()
    {
        return [
        ];
    }

    public function boot()
    {
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
