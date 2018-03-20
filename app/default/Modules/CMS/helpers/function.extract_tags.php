<?php

function smarty_function_extract_tags($params, &$view)
{	
    if (empty($params['assign'])) {
        return '';
    }
    if (empty($params['from'])) {
        $view->assign($params['assign'], []);
        return '';
    }
    $tags = explode("\n", trim($params['from']));
    foreach ($tags as $k => $tag) {
        $tags[$k] = explode(": ", $tag, 2);
        array_walk($tags[$k], 'trim');
    }
    $view->assign($params['assign'], $tags);
}