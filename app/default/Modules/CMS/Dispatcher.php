<?php

namespace Pina\Modules\CMS;

class Dispatcher
{

    public function dispatch($resource)
    {
        if (empty($resource)) {
            return null;
        }
        
        $ru = ResourceUrlGateway::instance()
            ->whereBy('url', $resource)
            ->select('resource_id')
            ->innerJoin(
                ResourceTypeGateway::instance()->on('id', 'resource_type_id')
                ->select('type')
            )
            ->first();
        
        if (!empty($ru)) {
            return $ru['type'].'/'.$ru['resource_id'];
        }
        
        $redirect = ResourceUrlGateway::instance()
            ->innerJoin(
                ResourceUrlHistoryGateway::instance()->on('resource_id')->whereBy('url', $resource)
            )
            ->value('url');

        if (!empty($redirect)) {
            header('HTTP/1.1 301 Moved Permanently');
            header("Location: /".$redirect);
            exit;
        }
        
        return null;
    }

}
