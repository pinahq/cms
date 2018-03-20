<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceUrlGateway extends TableDataGateway
{

    protected static $table = 'resource_url';
    protected static $fields = array(
        'resource_id' => "int(10) NOT NULL DEFAULT 0",
        'resource_type_id' => "int(10) NOT NULL DEFAULT 0",
        'url' => "varchar(1000) NOT NULL DEFAULT ''",
        'resource_enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
        'KEY url_type' => ['url', 'resource_type_id'],
    );

    public function getTriggers()
    {
        return [
            [
                $this->getTable(),
                'after update',
                "
                    IF (OLD.url <> NEW.url) THEN
                        DELETE FROM resource_url_history WHERE url = OLD.url;
                        INSERT INTO resource_url_history SET resource_id = OLD.resource_id, url = OLD.url;
                    END IF;
                "
            ],
            [
                $this->getTable(),
                'after delete',
                '
                    IF (OLD.resource_id > 0) THEN
                        DELETE FROM resource_url_history WHERE resource_id = OLD.resource_id;
                    END IF;
                '
            ],
        ];
    }

}
