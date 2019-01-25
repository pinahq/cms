<?php

namespace Pina\Modules\Media;

use Pina\App;

class Media
{

    public static function getUploadedFile()
    {
        $file = array_shift($_FILES);

        if (!is_uploaded_file($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return null;
        }

        return new File($file['tmp_name'], $file['name'], $file['type']);
    }

    public static function getUrlCache($url, $fileName = null)
    {
        $tmpPath = App::tmp() . '/' . uniqid('download', true);
        $f = fopen($tmpPath, 'w');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $f);
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $r = curl_exec($ch);

        $info = curl_getinfo($ch);
        if ($info['http_code'] !== 200) {
            throw new \RuntimeException('Can`t download resource: ' . $url);
        }
        curl_close($ch);

        if (empty($fileName)) {
            $urlPath = parse_url($url, PHP_URL_PATH);
            $fileName = pathinfo($urlPath, PATHINFO_BASENAME);
        }
        return new File($tmpPath, $fileName, $info['content_type'] ?? null);
    }

    public static function getUrl($storageKey, $path)
    {
        if (empty($storageKey)) {
            return $path;
        }
        return static::getStorage($storageKey)->getUrl($path);
    }

    public static function getStorage($storageKey)
    {
        static $storages = array();

        if (!empty($storages[$storageKey])) {
            return $storages[$storageKey];
        }

        return $storages[$storageKey] = new Storage($storageKey);
    }

    public static function getStorageConfig($storageKey, $configKey)
    {
        $config = \Pina\Config::get('media');
        return $config[$storageKey][$configKey] ?? '';
    }

    public static function findUrl($url)
    {
        $mediaId = MediaGateway::instance()->whereBy('original_url', $url)->id();
        if (!empty($mediaId)) {
            return $mediaId;
        }
        
        $parsed = parse_url($url);
        if (empty($parsed['path'])) {
            return;
        }
        $db = App::container()->get(\Pina\DatabaseDriverInterface::class);
        return MediaGateway::instance()->where("'" . $db->escape($parsed['path']) . "' LIKE CONCAT('%', path)")->id();
    }

}
