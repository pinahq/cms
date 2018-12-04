<?php

namespace Pina\Modules\Cart;

abstract class CSVImport
{

    protected $schema = [];
    protected $delimiter = ',';
    protected $enclosure = '';
    protected $charset = 'utf8';
    protected $handle = null;
    protected $header = null;

    public function __construct($delimiter, $enclosure, $charset = 'utf8')
    {
        $this->delimiter = $delimiter;
        $this->charset = $charset;
        $this->enclosure = $enclosure;
        $this->schema = $this->getSchema();
    }

    abstract public function getSchema();

    protected function start()
    {
        $this->header = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure);
        foreach ($this->header as $key => $value) {
            $this->header[$key] = ($this->charset != 'utf8') ? iconv($this->charset, 'utf8', $value) : $value;
        }
    }

    protected function read()
    {
        $line = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure);
        if (empty($line)) {
            return null;
        }

        $item = [];
        foreach ($this->schema as $spec) {
            list($key, $title) = $spec;
            $value = '';
            foreach ($this->header as $index => $v) {
                if ($v == $title) {
                    $value = $line[$index] ? $line[$index] : '';
                    break;
                }
            }
            $item[$key] = ($this->charset != 'utf8') ? iconv($this->charset, 'utf8', $value) : $value;
        }

        return $item;
    }

    protected function finalize(&$data)
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function importFromString($csv)
    {
        $this->handle = fopen("php://memory", "r+");
        fwrite($this->handle, $csv);
        rewind($this->handle);
        
        $this->import();
    }
    
    public function importFromStream($handle)
    {
        $this->handle = $handle;
        
        $this->import();
    }
    
    public function importFromFile($file)
    {
        $this->handle = fopen($file, "r");
        
        $this->import();
    }

    public function import()
    {
        $this->start();

        $data = [];
        while ($item = $this->read()) {
            $data[] = $item;
        }

        $this->finalize($data);
    }

}
