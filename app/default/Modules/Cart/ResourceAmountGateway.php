<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

use Pina\Modules\CMS\ResourceGateway;

/*
INSERT IGNORE INTO resource_amount (resource_id, amount)
SELECT resource_id, 0 FROM resource;

INSERT INTO resource_amount (resource_id, amount)
SELECT r.resource_id, @amount := IFNULL(SUM(amount), 0) as amount
FROM  resource r
LEFT JOIN offer o ON o.resource_id = r.resource_id AND o.enabled = 'Y'
GROUP BY r.resource_id
ON DUPLICATE KEY UPDATE amount = @amount;


INSERT INTO resource_amount (resource_id, amount)
SELECT resource_parent_id, @amount := SUM(amount) as amount
FROM  resource_tree rt
LEFT JOIN offer o ON o.resource_id = rt.resource_id AND o.enabled = 'Y'
GROUP BY resource_parent_id
ON DUPLICATE KEY UPDATE amount = @amount;
*/

class ResourceAmountGateway extends TableDataGateway
{
    protected static $table = 'resource_amount';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'amount' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
        'KEY amount' => 'amount',
    );
    
    public function getTriggers()
    {
        $resourceGatewayTable = ResourceGateway::instance()->getTable();
        return [
            [
                $resourceGatewayTable,
                'after insert',
                "INSERT INTO resource_amount SET resource_id = NEW.id, amount = 0;"
            ],
            [
                $resourceGatewayTable,
                'after update',
                "
                    IF (OLD.parent_id <> NEW.parent_id) THEN
                        SET @amount = (SELECT amount FROM resource_amount WHERE resource_id = NEW.id);
                        IF (@amount <> 0) THEN
                            UPDATE resource_amount ra INNER JOIN (SELECT resource_parent_id FROM resource_tree rt WHERE resource_id = OLD.parent_id UNION SELECT OLD.parent_id as resource_parent_id) parents on parents.resource_parent_id = ra.resource_id SET amount = amount - @amount;
                            UPDATE resource_amount ra INNER JOIN (SELECT resource_parent_id FROM resource_tree rt WHERE resource_id = NEW.parent_id UNION SELECT NEW.parent_id as resource_parent_id) parents on parents.resource_parent_id = ra.resource_id SET amount = amount + @amount;
                        END IF;
                    END IF;
                "
            ],
            [
                $resourceGatewayTable,
                'after delete',
                ' 
                    IF (OLD.id > 0) THEN
                        SET @amount = (SELECT amount FROM resource_amount WHERE resource_id = OLD.id);
                        IF (@amount <> 0) THEN
                            UPDATE resource_amount ra INNER JOIN (SELECT resource_parent_id FROM resource_tree rt WHERE resource_id = OLD.parent_id UNION SELECT OLD.parent_id as resource_parent_id) parents on parents.resource_parent_id = ra.resource_id SET amount = amount - @amount;
                        END IF;
                    END IF;
                '
            ],
            
        ];
    }

    public function whereInStock()
    {
        return $this->where($this->makeByCondition(array('>', self::SQL_OPERAND_FIELD, 'amount', self::SQL_OPERAND_VALUE, 0)));
    }

}
