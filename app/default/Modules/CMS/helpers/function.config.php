<?php

use Pina\Modules\CMS\Config;

function smarty_function_config($params, &$view)
{	
    if (empty($params['key']) || empty($params['namespace'])) {
        return '';
    }
    
    $value = Config::get($params['namespace'], $params['key'], isset($params['delimiter'])?$params['delimiter']:false);
    
    if (empty($params['assign'])) {
        return $value;
    }
    
    $view->assign($params['assign'], $value);
    return '';
}