<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

//INSERT INTO resource_in_stock SELECT resource_id FROM resource_amount WHERE amount > 0;
class ResourceInStockGateway extends TableDataGateway
{
    protected static $table = 'resource_in_stock';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
    );
    
    public function getTriggers()
    {
        $resourceAmountTable = ResourceAmountGateway::instance()->getTable();
        return [
            [
                $resourceAmountTable,
                'after insert',
                "
                    IF (NEW.amount > 0) THEN
                        INSERT INTO resource_in_stock SET resource_id=NEW.resource_id;
                    END IF;
                "
            ],
            [
                $resourceAmountTable,
                'after update',
                "
                    IF (NEW.amount > 0 AND OLD.amount <= 0) THEN
                        INSERT INTO resource_in_stock SET resource_id=NEW.resource_id;
                    END IF;
                    
                    IF (NEW.amount <= 0 AND OLD.amount > 0) THEN
                        DELETE FROM resource_in_stock WHERE resource_id=NEW.resource_id;
                    END IF;
                "
            ],
            [
                $resourceAmountTable,
                'after delete',
                ' 
                    IF (OLD.amount > 0) THEN
                        DELETE FROM resource_in_stock WHERE resource_id=OLD.resource_id;
                    END IF;
                '
            ],
            
        ];
    }
}
