<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

use Pina\Modules\Images\ImageGateway;

class ResourceImageGateway extends TableDataGateway
{
    protected static $table = 'resource_image';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'image_id' => "INT(10) NOT NULL DEFAULT 0",
        'order' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => array('resource_id', 'image_id')
    );
    
    public function withImage()
    {
        return $this->innerJoin(
            ImageGateway::instance()->on('id', 'image_id')
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
