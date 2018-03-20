<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('resources/:resource_id/meta');

$resourceId = Request::input('resource_id');


$meta = [];
if ($resourceId) {
    $meta = ResourceMetaGateway::instance()->find($resourceId);
}

if (empty($meta['title']) && empty($meta['description']) && empty($meta['keywords'])) {
    $config = Config::getNamespace(__NAMESPACE__);

    $title = \Pina\Request::getPlace('title');
    if ($title && $config['company_title']) {
        $title = $config['company_title'] . ' - ' . $title;
    }
    $meta = [
        'title' => $title ? $title : $config['meta_title'],
        'description' => $config['meta_description'],
        'keywords' => $config['meta_keywords'],
    ];

}

return [
    'meta' => $meta,
    'app_resource' => \Pina\App::resource(),
];
