<?php

namespace Pina\Modules\Images;

use Pina\TableDataGateway;

class ImageGateway extends TableDataGateway
{
    protected static $table = "image";
    protected static $fields = array(
        'id' => "INT(11) NOT NULL AUTO_INCREMENT",
        'original_id' => "INT(11) NOT NULL default '0'",
        'hash' => "VARCHAR(128) NOT NULL default ''",
        'filename' => "VARCHAR(255) NOT NULL default ''", //если картинка хранится у нас, то здесь её имя файла, которое однозначно идентифицирует его
        'url' => "VARCHAR(255) NOT NULL default ''", //если картинка хранится не у нас, то это ссылка туда, где её можно скачать
		'original_url' => "VARCHAR(255) NOT NULL default ''",//откуда взялась картинка, если её укачивали по URL (чтобы повторно не укачивать потом)
        'width' => "INT(1) NOT NULL default '0'",
        'height' => "INT(1) NOT NULL default '0'",
        'type' => "VARCHAR(32) NOT NULL default ''",
        'size' => "INT(11) NOT NULL default '0'",
        'alt' => "varchar(120) NOT NULL DEFAULT ''",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'KEY filename' => 'filename',
    );
    
    public function filter($rules)
    {
        if (isset($rules["filter_width"]) && is_array($rules["filter_width"]) && count($rules["filter_width"]) == 2) {
            $start = (int)$rules["filter_width"][0];
            $end = (int)$rules["filter_width"][1];
            $this->whereBetween('width', $start, $end);
        }

        if (isset($rules["filter_height"]) &&
        is_array($rules["filter_height"]) && count($rules["filter_height"]) == 2) {
            $start = (int)$rules["filter_height"][0];
            $end = (int)$rules["filter_height"][1];
            $this->whereBetween('height', $start, $end);
        }

        if (isset($rules["type"]) && $rules["type"] != "" && $rules["type"] != "*") {
            $this->whereBy("type", $rules["type"]);
        }

        if (isset($rules['substring']) && $rules['substring'] != '') {
            $this->whereLike(array('filename', 'alt'), '%'.$rules["substring"].'%');
        }
        
        return $this;
    }

}
