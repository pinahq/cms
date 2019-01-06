<?php

namespace Pina\Modules\Media;

class File
{

    protected $path = null;
    protected $meta = [];

    public function __construct($path, $name, $mime)
    {
        $this->path = $path;
        if (!file_exists($this->path)) {
            throw new \RuntimeException('File does not exist: ' . $this->path);
        }
        $this->meta = [];
        $this->meta += $this->getImageProperties($this->path, $name, $mime);
        $this->meta += $this->getFileProperties($this->path);
        $this->meta['path'] = $this->generatePath($name);
    }

    public function moveToStorage($storageKey = '')
    {
        $this->saveToStorage($storageKey);
        $this->unlink();
    }

    public function saveToStorage($storageKey = '')
    {
        if (empty($storageKey)) {
            $storageKey = \Pina\Config::get('media', 'default');
        }
        
        $this->meta['storage'] = $storageKey;

        $storage = Media::getStorage($storageKey);

        $stream = fopen($this->path, 'r+');
        $storage->filesystem()->writeStream($this->meta['path'], $stream);
        fclose($stream);
    }

    public function unlink()
    {
        unlink($this->path);
        $this->path = null;
    }
    
    public function exists()
    {
        return file_exists($this->path);
    }

    public function saveMeta()
    {
        return MediaGateway::instance()->insertGetId($this->meta);
    }

    public function getImageWidth()
    {
        return $this->meta['width'] ?? null;
    }

    public function getImageHeight()
    {
        return $this->meta['height'] ?? null;
    }

    public function getMimeType()
    {
        return $this->meta['type'] ?? null;
    }

    public function getSize()
    {
        return $this->meta['size'] ?? null;
    }

    public function getHash()
    {
        return $this->meta['hash'] ?? null;
    }

    public function getStorageKey()
    {
        return $this->meta['storage'] ?? null;
    }

    public function getStoragePath()
    {
        return $this->meta['path'] ?? null;
    }

    protected function getFileProperties($path)
    {
        return [
            'size' => filesize($path),
            'hash' => md5_file($path),
        ];
    }

    protected function getImageProperties($path, $name, $type)
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

        if (empty($info['mime'])) {
            $info['mime'] = mime_content_type($path);
        }
        if (empty($info['mime'])) {
            $info['mime'] = $type;
        }

        return [
            'width' => $info[0] ?? 0,
            'height' => $info[1] ?? 0,
            'type' => $info['mime'],
        ];
    }

    protected function generatePath($name)
    {
//        $name = str_replace(' ', '-', $name);
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

}
