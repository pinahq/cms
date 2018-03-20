<?php

namespace Pina\Modules\Users;

use Pina\ModuleInterface;

use Pina\Route;
use Pina\Access;
use Pina\Composer;
use Pina\Event;

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
        return 'Users';
    }

    public function http()
    {
        Access::permit('/users/:user_id', 'self');

        $user = Auth::user();
        if (!empty($user)) {
            Route::context("user_id", $user['id']);

            Access::addCondition('self', array('user_id' => $user['id']));

            Access::addGroup($user['group']);
        }

        Composer::placeView('sidebar::catalog', 'users/block', array('display' => 'sidebar'));
        Composer::placeView('header::nav1', 'users/block', array('display' => 'nav'));

        return [
            '/auth',
            '/cp/auth',
            '/cp/users',
            '/cp/auth-history',
            '/password-recovery',
            '/registration',
            '/subscription',
            '/users',
        ];
    }
    
    public function cli()
    {
        
    }
    
    public function boot()
    {
        Event::subscribe($this, 'user.subscribed', 'subscription.mail');
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
