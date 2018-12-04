<?php

namespace Pina\Modules\CMS;

use Pina\ModuleInterface;
use Pina\App;
use Pina\Input;
use Pina\Event;
use Pina\Url;
use Pina\Access;
use Pina\Route;
use Pina\DispatcherRegistry;
use Pina\Composer;

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

        Access::permit('/users/:user_id', 'self');
        $user = \Pina\Modules\Auth\Auth::user();
        if (!empty($user)) {
            Route::context("user_id", $user['id']);

            Access::addCondition('self', array('user_id' => $user['id']));

            Access::addGroup($user['group']);
        }


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

        \Pina\Composer::placeView('config.menu', 'cp/:cp/modules/block', array('display' => 'nav'));
        \Pina\Composer::placeView('config.menu', 'cp/:cp/tag-types/block', ['display' => 'nav']);
        \Pina\Composer::placeView('config.menu', 'cp/:cp/menus/block', array('display' => 'nav'));
        \Pina\Composer::placeView('config.menu', 'cp/:cp/resource-types/block', array('display' => 'nav'));
        \Pina\Composer::placeModule('menu.list', 'cp/:cp/resource-types', array('display' => 'menu'));
        \Pina\Composer::placeModule('menu.news', 'cp/:cp/submissions', array('display' => 'menu', 'date' => 'today'));

        Composer::placeView('sidebar::catalog', 'users/block', array('display' => 'sidebar'));
        Composer::placeView('header::nav1', 'users/block', array('display' => 'nav'));

        //routing
        return [
            '/',
            '/errors',
            '/favicon',
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
            'password-recovery',
            'registration',
            'subscription',
            'users',
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
        App::container()->share(\Pina\Modules\Auth\UserInterface::class, User::class);
        
        ImportReaderRegistry::register('csv', __('CSV'), \Pina\Modules\CMS\CSVImportReader::class);
        ImportReaderRegistry::register('excel', __('Excel Spreadsheet'), \Pina\Modules\CMS\ExcelImportReader::class);
        
        Event::subscribe($this, 'submission.created');
        Event::subscribe($this, 'user.subscribed', 'subscription.mail');
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
