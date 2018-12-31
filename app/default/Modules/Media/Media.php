<?php

namespace Pina\Modules\Media;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\AdapterInterface;
use Pina\Arr;

class Media
{

    public static function upload($targetStorageKey = null)
    {
        $file = array_shift($_FILES);

        if (!is_uploaded_file($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return null;
        }
        
        return static::save($targetStorageKey, $file['tmp_name'], $file['name'], $file['type']);
    }
    
    public static function save($targetStorageKey, $path, $name, $type)
    {
        $data = [];
        $data += static::getImageProperties($path, $name, $type);
        $data += static::getFileProperties($path);

        $config = \Pina\Config::get('media');
        $storageKey = $targetStorageKey ?? $config['default'];
        
        if (!isset($config[$storageKey])) {
            throw new \RuntimeException('Wrong target storage');
        }

        $data['storage'] = $storageKey;
        $data['path'] = static::generatePath($name);
        
        $storage = static::getStorage($storageKey);

        $stream = fopen($path, 'r+');
        $storage->filesystem()->writeStream($data['path'], $stream);
        fclose($stream);
        
        return MediaGateway::instance()->insertGetId($data);
    }

    public static function getImageProperties($path, $name, $type)
    {
        $info = getimagesize($path);
        if (empty($info)) {
            $pathInfo = pathinfo($name);
            if ($pathInfo['extension'] == 'ico') {
                $info = array(
                    0 => 0,
                    1 => 0,
                    'mime' => 'image/vnd.microsoft.icon'
                );
            }
        }

        return [
            'width' => $info[0] ?? 0,
            'height' => $info[1] ?? 0,
            'type' => $info['mime'] ?? ($type ?? ''),
        ];
    }
    
    public static function generatePath($name)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $code = "";
        $length = mt_rand(8, 32);

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $basename = pathinfo($name, PATHINFO_FILENAME);

//        $token = $code . '.' . mt_rand();
        $token = $code;
        
        $dir = substr($token, 0, 2) . '/' . substr($token, 2, 2) . '/';
        $filename = $basename . '.' . substr($token, 4) . '.' . $ext;

        return $dir . $filename;
    }

    public static function getFileProperties($path)
    {
        return [
            'size' => filesize($path),
            'hash' => md5_file($path),
        ];
    }
    
    public static function getUrl($storageKey, $path)
    {
        if (empty($storageKey)) {
            return $path;
        }
        return static::getStorage($storageKey)->getUrl($path);
    }
    
    protected static function getStorage($storageKey)
    {
        static $storages = array();
        
        if (!empty($storages[$storageKey])) {
            return $storages[$storageKey];
        }
        
        return $storages[$storageKey] = new Storage($storageKey);
    }

}
