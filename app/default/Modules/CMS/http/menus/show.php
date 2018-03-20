<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('menus/:key');

$key = Request::input('key');
$title = Request::input('title');

list($title, $items) = Menu::get($key, $title);

return ['title' => $title, 'menu_items' => $items];