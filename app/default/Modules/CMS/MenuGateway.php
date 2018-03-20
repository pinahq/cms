<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class MenuGateway extends TableDataGateway
{
	protected static $table = 'menu';

	protected static $fields = [
		'key' => "varchar(16) NOT NULL DEFAULT ''",
		'title' => "varchar(255) NOT NULL DEFAULT ''",
	];

	protected static $indexes = [
		'PRIMARY KEY' => 'key',
	];

}