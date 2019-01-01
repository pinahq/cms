<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

use Pina\Modules\Images\ImageGateway;

class ResourceMediaGateway extends TableDataGateway
{
    protected static $table = 'resource_media';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'media_id' => "INT(10) NOT NULL DEFAULT 0",
        'order' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => array('resource_id', 'media_id')
    );
    
    public function withMedia()
    {
        return $this->innerJoin(
            MediaGateway::instance()->on('id', 'media_id')
                ->select('id')
                ->select('original_id')
                ->select('hash')
                ->select('filename')
                ->select('url')
                ->select('width')
                ->select('height')
                ->select('type')
                ->select('size')
                ->select('alt')
        );
    }

}
