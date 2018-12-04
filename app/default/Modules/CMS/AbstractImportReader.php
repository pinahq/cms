<?php

namespace Pina\Modules\CMS;

interface AbstractImportReader extends \Iterator
{

    public function __construct($file, $headerRow, $startRow);

    public function header();

}
