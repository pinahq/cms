<?php

namespace Pina\Modules\Media;

use Pina\TableDataGateway;

class MediaGateway extends TableDataGateway
{

    protected static $table = "media";
    protected static $fields = array(
        'id' => "INT(11) NOT NULL AUTO_INCREMENT",
        'storage' => "VARCHAR(16) NOT NULL default ''",
        'path' => "VARCHAR(255) NOT NULL default ''",
        'original_url' => "VARCHAR(255) NOT NULL default ''", //откуда взялась картинка, если её укачивали по URL (чтобы повторно не укачивать потом)
        'hash' => "VARCHAR(128) NOT NULL default ''",
        'width' => "INT(11) NOT NULL default '0'",
        'height' => "INT(11) NOT NULL default '0'",
        'type' => "VARCHAR(32) NOT NULL default ''",
        'size' => "INT(11) NOT NULL default '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY' => ['storage', 'path'],
    );

}
