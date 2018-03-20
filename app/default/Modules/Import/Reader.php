<?php

namespace Pina\Modules\Import;


class Reader {

    private static $formatReaders = [
        'csv' => 'Pina\Modules\Import\CSVReader',
        'excel' => 'Pina\Modules\Import\ExcelReader',
        'yml' => 'Pina\Modules\Import\YMLReader',
    ];

    public static function getReader ($fileFormat, $file, $headerRow, $startRow) {

        if (!file_exists($file)) {
            \Pina\Log::error('import', 'File not found: '.$file);
            return false;
        }

        if (empty($fileFormat)) {
            \Pina\Log::error('import', 'Format have not been specified');
            return false;
        }

        if (!isset(self::$formatReaders[$fileFormat])) {
            \Pina\Log::error("import", 'Unknown file format ' . $fileFormat . '!');
            return null;
        }
        
        $readerClass = self::$formatReaders[$fileFormat];

        return new $readerClass($file, $headerRow, $startRow);
    }

    public static function getAvailableFormats() {
        $rs = [];
        
        foreach (self::$formatReaders as $k => $readerClass) {
            $title = call_user_func([$readerClass, 'title']);
            $rs[$k] = $title;
        }
        
        return $rs;
    }
}