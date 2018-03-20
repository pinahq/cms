<?php

namespace Pina\Modules\Import;

use Pina\App;

class FileUploader
{
    //Доступные типы файлов
    private static $validFileTypes = [
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'text/xml' => 'xml',
        'text/csv' => 'csv'
    ];

    //Проверяет загруженный файл
    public static function validate($fileInput)
    {
        if (empty($_FILES[$fileInput]) || !is_array($_FILES[$fileInput])) {
            return false;
        }

        if (!isset($_FILES[$fileInput]['type']) || !isset(self::$validFileTypes[$_FILES[$fileInput]['type']])) {
            return false;
        }

        if ($_FILES[$fileInput]['size'] == 0) {
            return false;
        }

        return true;
    }

    public static function validateSavedFile($filePath) {
        return file_exists($filePath);
    }

    //Перемещает загруженный файл
    public static function move($fileInput)
    {
        $uploaddir = App::tmp() . '/import';

        $type = $_FILES[$fileInput]['type'];
        $ext = self::$validFileTypes[$type];

        if (!$ext) {
            return array(null, null);
        }

        $uname = md5(preg_replace('/\D/', '', microtime(true)) . rand(1, 1000)) . '.' . $ext;
        
        $first = substr($uname, 0, 2);
        $second = substr($uname, 2, 2);

        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0777, true);
        }
        
        @mkdir($uploaddir . "/" . $first, 0777);
        @chmod($uploaddir, 0777);
        @mkdir($uploaddir . "/" . $first . "/" . $second, 0777);
        @chmod($uploaddir, 0777);
        
        $dest = $uploaddir . '/' . $first . "/" . $second . '/' . $uname;

        if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $dest)) {
            return array($dest, $_FILES[$fileInput]['name']);
        } else {
            \Pina\Log::error('import', 'Can not create temporary file ' . $dest . ' based on ' . $_FILES[$fileInput]['tmp_name']);
            return array(null, null);
        }
    }
}
