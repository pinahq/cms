<?php

namespace Pina\Modules\CMS;

use Pina\ModuleInterface;

use Pina\App;
use Pina\Input;
use Pina\Event;
use Pina\Url;
use Pina\Access;
use Pina\Route;
use Pina\Request;
use Pina\DispatcherRegistry;

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
        return 'CMS';
    }

    public function http()
    {
        
        Route::context('cp', 'ru');

        Access::permit('/cp/:cp', 'root');
        
        //Do not dispatch system resources
        if (!in_array(App::resource(), ['sitemap.xml', 'robots.txt']) && !preg_match('/^sitemap\/.*\.xml/si', App::resource())) {
            DispatcherRegistry::register(new Dispatcher());
        }

        $method = Input::getMethod();
        if ($method == 'get') {
            $resource = App::resource();
            $info = pathinfo($resource);
            $extension = !empty($info['extension']) ? $info['extension'] : '';
            if (in_array($extension, array('', 'html')) && (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {
                $r = App::resource();
                list($preg, $map) = Url::preg('cp/:cp');
                if (preg_match("/^" . $preg . "/si", $r)) {
                    App::setDefaultLayout('cp');
                }
            }
        }
        
        \Pina\Composer::placeView('config.menu', 'cp/:cp/tag-types/block', ['display' => 'nav']);
        \Pina\Composer::placeView('config.menu', 'cp/:cp/menus/block', array('display' => 'nav'));
        \Pina\Composer::placeView('config.menu', 'cp/:cp/resource-types/block', array('display' => 'nav'));
        
        //routing
        return [
            'heading-content',
            'text-content',
            'list-content',
            'pages',
            'resources',
            'search',
            'submissions',
            'sitemap',
            'robots',
            'favicon-link',
            'content',
            'menus',
            'image-content',
            'gallery-content',
            'resource-list-content',
            'cp',
        ];
    }
    
    public function cli()
    {
        return [
            'resources'
        ];
    }
    
    public function boot()
    {
        Event::subscribe($this, 'submission.created');
    }
    
}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}