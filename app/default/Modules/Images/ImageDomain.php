<?php

namespace Pina\Modules\Images;

use Pina\Log;

class ImageDomain extends \Pina\FileManager {

    public static $dir = 'images';
    protected static $approved = array(
        'jpg', 'jpeg', 'png', 'gif', 'ico'
    );
    protected static $table = '\Pina\Modules\Images\ImageGateway';

    public static function prepareData($filename, $ext = false, $originalImageId = 0) {
        if (empty(static::$table)) {
            return 0;
        }
        $path = static::getFilePath($filename, $ext);
        $imgInfo = getimagesize($path);
        if (empty($imgInfo)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] !== 'ico' || !file_exists($path)) {
                return false;
            }
            $imgInfo = array(
                0 => 0,
                1 => 0,
                'mime' => 'image/vnd.microsoft.icon'
            );
        }
        $data['original_id'] = $originalImageId;
        $data['hash'] = md5_file($path);
        list($data['width'], $data['height']) = $imgInfo;
        $data['type'] = $imgInfo['mime'];
        $data['size'] = filesize(static::getFilePath($filename, $ext));
        $gw = new static::$table;
        $id = $gw->whereFields($data)->id();
        if (!empty($id)) {
            @unlink($path);
            return $id;
        }
        $data['filename'] = $filename . (empty($ext) ? '' : ('.' . $ext));
        
        $id = $gw->insertGetId($data);
        return $id;
    }

    public static function prepareUrlData($originalUrl, $filename, $ext = false, $originalImageId = 0) {
        if (empty(static::$table)) {
            return 0;
        }
        $path = static::getFilePath($filename, $ext);
        $imgInfo = getimagesize($path);
        if (empty($imgInfo)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] !== 'ico' || !file_exists($path)) {
                return false;
            }
            $imgInfo = array(
                0 => 0,
                1 => 0,
                'mime' => 'image/vnd.microsoft.icon'
            );
        }
        $data['original_id'] = $originalImageId;
        $data['hash'] = md5_file($path);
        $data['filename'] = $filename . (empty($ext) ? '' : ('.' . $ext));
        list($data['width'], $data['height']) = $imgInfo;
        $data['type'] = $imgInfo['mime'];
        $data['size'] = filesize(static::getFilePath($filename, $ext));
        $data['original_url'] = $originalUrl;
        $gw = new static::$table;
        $id = $gw->insertGetId($data);
        return $id;
    }

    public function parseImageId($text) 
    {
        preg_match_all('/(<img[^<>]+src[ ]*=[ ]*["\'])([^"\']*images\/)([^&^"^\']*)(["\'])/iUS', $text, $matches);
        if (!empty($matches) && !empty($matches[3]) && is_array($matches[3])) {
            foreach ($matches[3] as $img) {
                $pathinfo = pathinfo($img);
                require_once PATH . 'modules/default/images/tables/image.php';
                $gw = new static::$table;
                $imageId = $gw->whereBy('filename', $pathinfo['basename'])->id();
                if (!empty($imageId))
                    return $imageId;
            }
        }
        return 0;
    }

    static function saveUrl($source, $filename) 
    {
        if (empty($source)) {
            return;
        }

        $pathinfo = pathinfo($filename);
        $ext = !empty($pathinfo["extension"]) ? strtolower($pathinfo["extension"]) : '';

        $souce_filename = strtolower($pathinfo["filename"]);
        $filename = static::newFileName($souce_filename, $ext);

        $filename = static::prepareFilename($source, $filename);

        $content = @file_get_contents($source);
        if (empty($content)) {
            return false;
        }

        $filePath = static::getFilePath($filename);
        static::prepareDir($filename);

        if (!@file_put_contents($filePath, $content)) {
            unset($content);
            Log::error('image', 'ImageDomain can not write to file ' . $filePath);
            return false;
        }
        unset($content);

        return static::prepareUrlData($source, $filename);
    }

}
