<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use SplFileInfo;
use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;

use function clearstatcache;
use function CoStack\Lib\concat_paths;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function is_callable;
use function is_dir;
use function is_numeric;
use function json_decode;
use function json_encode;
use function mkdir;
use function sprintf;
use function time;
use function unlink;

use const JSON_THROW_ON_ERROR;

readonly class Cache implements CacheInterface
{
    private string $cacheRoot;

    public function __construct(private Config $config, private PackageExtras $packageExtras)
    {
        $this->cacheRoot = $packageExtras->getPath($this->packageExtras->rootPackageName, 'cache')
            ?? concat_paths(getenv('VXVR_BS_ROOT'), 'var/cache');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $cacheFile = concat_paths($this->cacheRoot, $key);

        // Cache never existed
        if (!file_exists($cacheFile)) {
            if (is_callable($default)) {
                $default = $default();
            }
            $this->set($key, $default);
            return $default;
        }

        // Check if cache is expired
        $cacheEntry = json_decode(file_get_contents($cacheFile), true, 512, JSON_THROW_ON_ERROR);
        if (is_numeric($cacheEntry['ttl']) && $cacheEntry['ttl'] > 0) {
            $fileInfo = new SplFileInfo($cacheFile);
            $livesUntil = $fileInfo->getCTime() + $cacheEntry['ttl'];
            if ($livesUntil < time()) {
                // Cache expired, set new
                if (is_callable($default)) {
                    $default = $default();
                }
                $this->set($key, $default);
                return $default;
            }
        }

        // Cache okay, return value
        return $cacheEntry['value'];
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        if (is_callable($value)) {
            $value = $value();
        }
        if ($ttl instanceof DateInterval) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $ttl = $ttl->format('s');
        }
        $cacheEntry = [
            'ttl' => $ttl,
            'value' => $value,
        ];
        $cacheFile = concat_paths($this->cacheRoot, $key);
        $path = dirname($cacheFile);
        clearstatcache(false, $cacheFile);
        if (!is_dir($path) && mkdir($path, $this->config->get('folderPermissions'), true) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        return (bool)file_put_contents($cacheFile, json_encode($cacheEntry, JSON_THROW_ON_ERROR));
    }

    public function delete(string $key): bool
    {
        $cacheFile = concat_paths($this->cacheRoot, $key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            return true;
        }
        return false;
    }

    public function clear(): bool
    {
        if (is_dir($this->cacheRoot)) {
            exec('rm -rf ' . escapeshellarg($this->cacheRoot));
            return true;
        }
        return false;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $value) {
            $this->set($value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has(string $key): bool
    {
        $cacheFile = concat_paths($this->cacheRoot, $key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        // Check if cache is expired and if, delete it.
        $cacheEntry = json_decode(file_get_contents($cacheFile), true, 512, JSON_THROW_ON_ERROR);
        if (is_numeric($cacheEntry['ttl']) && $cacheEntry['ttl'] > 0) {
            $fileInfo = new SplFileInfo($cacheFile);
            $livesUntil = $fileInfo->getCTime() + $cacheEntry['ttl'];
            if ($livesUntil < time()) {
                unlink($cacheFile);
                return false;
            }
        }

        return true;
    }
}
