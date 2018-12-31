<?php

namespace Pina\Modules\Media;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use Pina\Arr;

class Storage
{

    protected $config = [];
    protected $filesystem = null;

    public function __construct($storageKey = null)
    {
        $config = \Pina\Config::get('media');
        $storageKey = $targetStorageKey ?? $config['default'];

        if (!isset($config[$storageKey])) {
            throw new \RuntimeException('Wrong target storage');
        }

        $this->config = $config[$storageKey];
    }

    public function filesystem()
    {
        if (empty($this->filesystem)) {
            $adapter = $this->resolveAdapter();
            $this->filesystem = $this->createFlysystem($adapter);
        }
        return $this->filesystem;
    }

    public function adapter()
    {
        $adapter = $this->filesystem()->getAdapter();
        if ($adapter instanceof CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }
        return $adapter;
    }

    public function resolveAdapter(array $config)
    {
        switch ($config['driver']) {
            case 'local': return $this->createLocalDriver($config);
            case 's3': return $this->createS3Driver($config);
        }

        throw new \Exception('Can`t create filesystem adapter.');
    }

    protected function createLocalDriver(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;
        return new LocalAdapter($config['root'], LOCK_EX, $links, $permissions);
    }

    protected function createS3Driver(array $config)
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

    public function getUrl($path)
    {
        switch ($config['driver']) {
            case 'local': return $this->getLocalDriverUrl($path);
            case 's3': return $this->getS3DriverUrl($path);
        }
        throw new RuntimeException('This driver does not support retrieving URLs.');
    }

    public function getLocalDriverUrl(string $path)
    {
        return $this->concatPathToUrl($this->config['url'] ?? '', $path);
    }

    public function getS3DriverUrl(string $path)
    {
        $adapter = $this->adapter();
        // If an explicit base URL has been set on the disk configuration then we will use
        // it as the base URL instead of the default path. This allows the developer to
        // have full control over the base path for this filesystem's generated URLs.
        if (!is_null($this->config['url'])) {
            return $this->concatPathToUrl($this->config['url'], $adapter->getPathPrefix() . $path);
        }
        return $adapter->getClient()->getObjectUrl(
                        $adapter->getBucket(), $adapter->getPathPrefix() . $path
        );
    }

    protected function concatPathToUrl($url, $path)
    {
        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }

    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);

        return new Flysystem($adapter, count($config) > 0 ? $config : null);
    }

}
