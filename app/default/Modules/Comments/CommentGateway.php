<?php

namespace Pina\Modules\Comments;

use Pina\TableDataGateway;

class CommentGateway extends TableDataGateway
{

    protected static $table = 'comment';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'resource_id' => "int(10) NOT NULL DEFAULT '0'",
        'user_id' => "int(10) NOT NULL DEFAULT '0'",
        'text' => "text NOT NULL",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'KEY resource_id' => 'resource_id',
    );

}
