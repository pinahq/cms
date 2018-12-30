<?php

namespace Pina\Modules\Media;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\AdapterInterface;
use Pina\Arr;

class Uploader
{

    public function upload()
    {
        $file = array_shift($_FILES);

        if (!is_uploaded_file($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return null;
        }

        $info = getimagesize($file['tmp_name']);
        if (empty($info)) {
            $pathInfo = pathinfo($file['name']);
            if ($pathInfo['extension'] == 'ico') {
                $info = array(
                    0 => 0,
                    1 => 0,
                    'mime' => 'image/vnd.microsoft.icon'
                );
            }
        }
        $data['width'] = $info[0] ?? 0;
        $data['height'] = $info[1] ?? 0;
        $data['type'] = $info['mime'] ?? ($file['type'] ?? '');
        $data['size'] = filesize($file['tmp_name']);
        $data['hash'] = md5_file($file['tmp_name']);


        $config = \Pina\Config::get('media');
        $storageKey = $config['default'];
        
        $adapter = null;
        switch ($config[$storageKey]['driver']) {
            case 'local': $adapter = $this->createLocalDriver($config[$storageKey]);
            case 's3': $adapter = $this->createLocalDriver($config[$storageKey]);
        }
        
        if (empty($adapter)) {
            throw new \Exception('Can`t create filesystem adapter.');
        }
        
        $data['storage'] = $storageKey;
        $data['path'] = $this->generatePath($file['name']);
        
        $fs = $this->createFlysystem($adapter, $config['default']);

        $stream = fopen($file['tmp_name'], 'r+');
        $fs->writeStream($data['path'], $stream);
        fclose($stream);
    }
    
    public function url($fs, $path)
    {
        $adapter = $fs->getAdapter();
        if ($adapter instanceof CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }
        if (method_exists($adapter, 'getUrl')) {
            return $adapter->getUrl($path);
//        } elseif (method_exists($this->driver, 'getUrl')) {
//            return $this->driver->getUrl($path);
        } elseif ($adapter instanceof AwsS3Adapter) {
            return $this->getAwsUrl($adapter, $path);
        } elseif ($adapter instanceof RackspaceAdapter) {
            return $this->getRackspaceUrl($adapter, $path);
        } elseif ($adapter instanceof LocalAdapter) {
            return $this->getLocalUrl($path);
        } else {
            throw new RuntimeException('This driver does not support retrieving URLs.');
        }
    }

    public function createLocalDriver(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;
        return new LocalAdapter($config['root'], LOCK_EX, $links, $permissions);
    }
    
    public function createS3Driver(array $config)
    {
        $s3Config = $this->formatS3Config($config);
        $root = $s3Config['root'] ?? null;
        $options = $config['options'] ?? [];
        return new S3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root, $options);
    }
    
    protected function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }
        return $config;
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface  $adapter
     * @param  array  $config
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);

        return new Flysystem($adapter, count($config) > 0 ? $config : null);
    }

}
