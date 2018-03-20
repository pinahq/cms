<?php

namespace Pina\Modules\Import;

interface AbstractReader extends \Iterator
{

    public function __construct($file, $headerRow, $startRow);

    public static function title();

    public function header();

}
