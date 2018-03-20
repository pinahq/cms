<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class ImportOfferTagGateway extends TableDataGateway
{
    protected static $table = 'import_offer_tag';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'offer_id' => "int(11) NOT NULL DEFAULT '0'",
        'tag_id' => "int(11) NOT NULL DEFAULT '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => ['import_id', 'offer_id', 'tag_id'],
    );

}