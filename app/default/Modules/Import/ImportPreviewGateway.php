<?php

namespace Pina\Modules\Import;

use Pina\TableDataGateway;


class ImportPreviewGateway extends TableDataGateway
{
    protected static $table = 'import_preview';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'row' => "int(11) NOT NULL DEFAULT '0'",
        'error' => "enum('Y','N') DEFAULT 'N'",
        'existed' => "enum('Y','N') DEFAULT 'N'",
        'preview' => "TEXT NOT NULL"//json
    );
    protected static $indexes = array(
        'PRIMARY KEY' => array('import_id', 'row'),
        'KEY error' => 'error'
    );

    public function filter($rules)
    {
        if (!empty($rules['filter']) && $rules['filter'] == 'errors') {
            $this->whereHasErrors();
        }
        
        return $this;
    }

    public function whereHasErrors()
    {
        return $this->whereBy('error', 'Y');
    }

    public function whereWithoutErrors()
    {
        return $this->whereBy('error', 'N');
    }

    public function whereNew($accountId)
    {
        return $this->whereBy('existed', 'N');
    }

    public function whereExists($accountId)
    {
        return $this->whereBy('existed', 'Y');
    }

    public function whereStock()
    {
        return $this->whereBy('product_variant_stock', array('in', 'backorder', 'Н', 'З'));
    }
}
