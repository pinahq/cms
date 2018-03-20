<?php

namespace Pina\Modules\CMS;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;

class XLSWriter
{

    private $excel = null;
    private $sheet = null;

    public function __construct(&$data, $callback = null)
    {
        $this->createSheet();
        if ($callback) {
            $callback($this->sheet);
        } else {
            $headerRange = $this->getHeaderRange($data);
            $this->formatHeader($headerRange);
        }
        $this->loadData($data);
    }
    
    public function setSheetTitle($sheetTitle)
    {
        if (empty($sheetTitle)) {
            return;
        }
        $this->sheet->setTitle($sheetTitle);
    }

    public function download($fname = false)
    {
        if (empty($fname)) {
            $fname = 'file';
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fname . '.xls"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $writer->save('php://output');
    }
    
    public function save($fname)
    {
        if (empty($fname)) {
            $fname = 'file';
        }
        $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $writer->save($fname.".xls");
    }
    
    private function createSheet()
    {
        $this->excel = new PHPExcel();
        $this->sheet = $this->excel->getActiveSheet();
    }
    
    private function formatHeader($headerRange)
    {
        $this->sheet->getStyle($headerRange)->getFont()->setBold(true);
        $this->sheet->getStyle($headerRange)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $this->sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('cccccc');
        $this->sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);        
        $this->sheet->getRowDimension('1')->setRowHeight(40);
    }

    private function getHeaderRange(&$data)
    {
        $count = count(reset($data)) - 1;
        $base = ord('Z') - ord('A') + 1;
        
        $rangeInBase = base_convert($count, 10, $base);
        $rangeInBaseLength = strlen($rangeInBase);
        $range = '';
        for ($i = 0; $i < $rangeInBaseLength; $i++) {
            $char = strtoupper($rangeInBase[$i]);
            if ($char <= '9') {
                $diff = ord($char) - ord('0');
            } else {
                $diff = ord($char) - ord('A') + 10;
            }
            if (empty($range) && $rangeInBaseLength > 1) {
                $diff -= 1;
            }
            $range .= chr($diff + ord('A'));
        }
        return 'A1:'.$range.'1';
    }
    
    private function loadData(&$data)
    {
        $this->sheet->fromArray($data);
    }

}
