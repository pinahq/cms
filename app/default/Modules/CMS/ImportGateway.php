<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ImportGateway extends TableDataGateway
{

    protected static $table = 'import';
    protected static $fields = array(
        'id' => "int(11) NOT NULL AUTO_INCREMENT",
        'header_row' => "int(11) NOT NULL DEFAULT '0'",
        'start_row' => "int(11) NOT NULL DEFAULT '0'",
        'last_row' => "int(11) NOT NULL DEFAULT '0'",
        'path_delimiter' => "VARCHAR(12) NOT NULL DEFAULT '//'",
        'format' => "varchar(32) NOT NULL DEFAULT ''",
        'file_name' => "varchar(255) NOT NULL DEFAULT ''",
        'file_header' => "varchar(2500) NOT NULL DEFAULT ''",
        'path' => "varchar(255) NOT NULL DEFAULT ''",
        'header' => "varchar(2500) NOT NULL DEFAULT ''",
        'schema' => "text NULL",
        'replaces' => "mediumtext NULL",
        'settings' => "mediumtext NULL",
        'keys' => "varchar(512) NOT NULL DEFAULT ''",
        'status' => "enum('read','confirm','import','done','error') NOT NULL DEFAULT 'read'",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );

    public function setSchema($schema)
    {
        $fileHeader = json_decode($this->value('file_header'), true);
        $countHeader = count($fileHeader);
        $countSchema = count($schema);
        $iter = 0;
        if ($countSchema > $countHeader) {
            for ($i = $countSchema - 1; $i >= $countHeader; $i--) {
                if (empty($schema[$i])) {
                    unset($schema[$i]);
                } else {
                    break;
                }
            }
            $schema = array_values($schema);
        }
        
        return $this->update([
                "last_row" => 0,
                "status" => "read",
                "schema" => json_encode($schema, JSON_UNESCAPED_UNICODE)
        ]);
    }

    public function setReplaces($replaces)
    {
        return $this->update([
                "last_row" => 0,
                "status" => "read",
                "replaces" => json_encode($replaces, JSON_UNESCAPED_UNICODE),
        ]);
    }

}
