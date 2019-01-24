<?php

use \Pina\Modules\Media\Media;

function smarty_function_media_url($params, &$view)
{
    if (empty($params['storage']) || empty($params['path'])) {
        return '';
    }
    $r = Media::getUrl($params['storage'], $params['path']);
    if ($params['assign']) {
        $view->assign($params['assign'], $r);
        return '';
    }
    return $r;
}
