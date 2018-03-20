<?php

function smarty_function_slot($params, &$view)
{
    if (empty($params['name'])) {
        return '';
    }

    $resourceId = 0;

    if (isset($params['resource_id'])) {
        $resourceId = $params['resource_id'];
    } else {
        $resource = \Pina\App::resource();
        $resourceId = \Pina\Modules\CMS\ResourceUrlGateway::instance()->whereBy('url', $resource)->value('resource_id');
    }

    $cs = \Pina\Modules\CMS\ContentGateway::instance()
            ->whereBy('slot', $params['name'])
            ->whereBy('resource_id', $resourceId)
            ->select('id')
            ->select('text')
            ->select('params')
            ->select('order')
            ->innerJoin(
                    \Pina\Modules\CMS\ContentTypeGateway::instance()->on('id', 'content_type_id')->select('type')->select('title')
            )
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    
    $r = '<div class="content-slot" data-name="' . $params['name'] . '" data-single="' . intval($params['single']) . '" data-csrf-token="'.\Pina\CSRF::token('post').'"' . '>';
    
    foreach ($cs as $content) {
        
        $content['params'] = json_decode($content['params'], true);
        
        $c = \Pina\Templater::processView([
            'get' => 'cp/:cp/content/' . $content['id'],
            'content' => $content,
            'slot_single' => intval($params['single']),
            'display' => 'wrapper',
        ], $view);
        
        if (!$c) {
            $c = \Pina\Templater::processView([
                'get' => $content['type'] . '/' . $content['id'],
                'content' => $content,
            ], $view);
        }

        $r .= $c;
    }
    
    if (empty($params['single']) || empty($cs)) {

        $r .= \Pina\Templater::processView([
            'get' => 'cp/:cp/content-types/block',
            'display' => 'front-add-link',
            'slot' => $params['name'],
            'resource_id' => $resourceId,
        ], $view);
    
    }

    if ($params['assign']) {
        $view->assign($params['assign'], $r);
        return '';
    }

    $r .= '</div>';

    return $r;
}
