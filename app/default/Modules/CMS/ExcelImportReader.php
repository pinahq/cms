<?php

namespace Pina\Modules\CMS;

use PHPExcel_IOFactory;

class ExcelImportReader implements AbstractImportReader
{

    private $file = '';
    private $excelReader;
    private $loadedFile;
    private $activeSheet;
    private $highestRow = 0;
    private $headerRow = 0;
    private $startRow = 0;
    private $iterator = null;

    public function __construct($file, $headerRow, $startRow)
    {
        $this->file = $file;
        $this->headerRow = $headerRow;
        $this->startRow = $startRow;

        if (!file_exists($this->file)) {
            return;
        }

        $this->excelReader = PHPExcel_IOFactory::createReader($this->getExcelType());
        $this->excelReader->setReadDataOnly(true);
        $this->loadedFile = $this->excelReader->load($this->file);
        $this->loadedFile->setActiveSheetIndex(0);
        $this->activeSheet = $this->loadedFile->getActiveSheet();
        $this->highestRow = $this->activeSheet->getHighestRow();

        $this->iterator = $this->activeSheet->getRowIterator($this->startRow, $this->highestRow);
    }

    public function header()
    {
        $header = [];
        $rows = $this->activeSheet->getRowIterator($this->headerRow, $this->headerRow);

        foreach ($rows as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();

            $c = 1;
            foreach ($cellIterator as $cell) {
                $title = trim($cell->getCalculatedValue());

                //Параметр: Именовать пустые заголовки
                if (empty($title)) {
                    $title = 'R' . $this->headerRow . 'C' . $c;
                }

                $header[] = $title;
                $c++;
            }
        }

        return $header;
    }

    public function current()
    {
        if (empty($this->iterator)) {
            return [];
        }

        $row = $this->iterator->current();

        $cellIterator = $row->getCellIterator();

        $rowData = [];
        foreach ($cellIterator as $cell) {
            $rowData[] = trim($cell->getCalculatedValue());
        }

        return $rowData;
    }

    public function key()
    {
        if (empty($this->iterator)) {
            return 0;
        }

        return $this->iterator->key();
    }

    public function next()
    {
        if (empty($this->iterator)) {
            return;
        }

        return $this->iterator->next();
    }

    public function rewind()
    {
        if (empty($this->iterator)) {
            return;
        }

        return $this->iterator->rewind();
    }

    public function valid()
    {
        if (empty($this->iterator)) {
            return false;
        }

        return $this->iterator->valid();
    }

    private function getExcelType()
    {
        $info = pathinfo($this->file);
        $fileType = $info['extension'];

        switch ($fileType) {
            case 'xls':
                return 'Excel5';
            case 'xlsx':
                return 'Excel2007';
        }

        return '';
    }

}
