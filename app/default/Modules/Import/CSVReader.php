<?php

namespace Pina\Modules\Import;

use PHPExcel_IOFactory;
use Pina\Modules\CMS\Config;

class CSVReader implements AbstractReader
{

    private $file = '';
    private $handle = null;
    private $headerRow = 0;
    private $startRow = 0;
    private $index = 0;
    private $header = null;
    private $rowBuffer = null;
    private $charset = null;
    private $delimiter = null;

    public function __construct($file, $headerRow, $startRow)
    {
        $this->file = $file;
        $this->headerRow = $headerRow - 1;
        $this->startRow = $startRow - 1;
        
        $config = Config::getNamespace('Pina\Modules\Cart');

        $this->charset = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
        $this->delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
        
        $this->init();
        $this->readHeader();
        $this->moveToStart();
    }
    
    private function init()
    {
        if ($this->handle) {
            $this->close();
        }
        $this->handle = fopen($this->file, "r");
        $this->index = 0;
    }
    
    private function close()
    {
        fclose($this->handle);
        $this->handle = null;
    }

    public static function title()
    {
        return __('CSV');
    }

    public function header()
    {
        $header = [];
        foreach ($this->header as $index => $item) {
            if (empty($item)) {
                $item = '_'.$index;
            }
            $header[] = $item;
        }
        return $this->header;
    }

    public function current()
    {
        return $this->rowBuffer;
    }

    public function key()
    {
        $index = $this->index - $this->startRow;
        return $index >= 0 ? $index + 1: 0;
    }

    public function next()
    {
        $this->read();
        $this->index++;
    }

    public function rewind()
    {
        if ($this->index !== $this->startRow) {
            $this->init();
            $this->moveToStart();
        }
    }

    public function valid()
    {
        return !empty($this->rowBuffer);
    }
    
    private function read()
    {
        $this->rowBuffer = fgetcsv($this->handle, 0, $this->delimiter);
        if ($this->charset !== 'utf8' && is_array($this->rowBuffer)) {
            array_walk($this->rowBuffer, function(&$item) {$item = iconv($this->charset, 'utf8', $item);});
        }
        if (empty($this->rowBuffer)) {
            $this->close();
        }
    }

    private function readHeader()
    {
        if ($this->index > $this->headerRow) {
            return;
        }

        while ($this->index < $this->headerRow) {
            fgetcsv($this->handle, 0, $this->delimiter);
            $this->index ++;
        }

        $this->header = fgetcsv($this->handle, 0, $this->delimiter);
        if ($this->charset !== 'utf8' && is_array($this->header)) {
            array_walk($this->header, function(&$item) {$item = iconv($this->charset, 'utf8', $item);});
        }
        $this->index ++;
    }

    private function moveToStart()
    {
        if ($this->index > $this->startRow) {
            return;
        }

        while ($this->index < $this->startRow) {
            fgetcsv($this->handle, 0, $this->delimiter);
            $this->index ++;
        }
        $this->read();
    }

}
