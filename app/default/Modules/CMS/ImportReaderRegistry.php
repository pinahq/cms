<?php

namespace Pina\Modules\CMS;

use Pina\Log;

class ImportReaderRegistry
{
    protected static $titles = [];
    protected static $registry = [];

    public static function register($format, $title, $className)
    {
        static::$titles[$format] = $title;
        static::$registry[$format] = $className;
    }
    
    public static function get($fileFormat, $file, $headerRow, $startRow) {
        if (!file_exists($file)) {
            Log::error('import', 'File not found: '.$file);
            return false;
        }

        if (empty($fileFormat)) {
            Log::error('import', 'Format have not been specified');
            return false;
        }

        if (!isset(static::$registry[$fileFormat])) {
            Log::error("import", 'Unknown file format ' . $fileFormat . '!');
            return null;
        }
        
        
        
        $readerClass = static::$registry[$fileFormat];
        return new $readerClass($file, $headerRow, $startRow);
    }

    public static function getAvailableFormats() {
        return static::$titles;
    }
    
}