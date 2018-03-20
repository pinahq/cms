<?php

namespace Pina\Modules\CMS;

class Config {
    
    private static $cache = [];
    
    public static function get($ns, $key, $delimiter = false)
    {
        if (!isset(self::$cache[$ns])) {
            self::loadNamespace($ns);
        }
        $value = isset(self::$cache[$ns][$key])?self::$cache[$ns][$key]:'';

        if ($delimiter) {
            $values = explode($delimiter, $value);
            foreach ($values as $k => $v) {
                $values[$k] = trim($v);
            }
            return $values;
        }
        return $value;
    }
    
    public static function getNamespace($ns)
    {
        if (!isset(self::$cache[$ns])) {
            self::loadNamespace($ns);
        }
        
        return self::$cache[$ns];
    }
    
    private static function loadNamespace($ns)
    {
        self::$cache[$ns] = ConfigGateway::instance()
            ->whereNamespace($ns)
            ->column('value', 'key');
    }
    
}