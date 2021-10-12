<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures;

use Psr\SimpleCache\CacheInterface;

use function serialize;
use function unserialize;

trait CacheAwareTrait
{
    private static $permanentCacheStorage = [];

    /**
     * @return CacheInterface
     */
    private function getCache(bool $permanentStorage = false) : CacheInterface
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->storage = [];

        if ($permanentStorage) {
            $cache->storage = &self::$permanentCacheStorage;
        }

        $cache->method('get')->will($this->returnCallback(function ($key) use ($cache) {
            return isset($cache->storage[$key]) ? unserialize($cache->storage[$key]) : null;
        }));

        $cache->method('has')->will($this->returnCallback(function ($key) use ($cache) {
            return isset($cache->storage[$key]);
        }));

        $cache->method('set')->will($this->returnCallback(function ($key, $value) use ($cache) {
            $cache->storage[$key] = serialize($value);
        }));

        return $cache;
    }
}
