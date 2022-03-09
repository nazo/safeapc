<?php

namespace SafeApc;

/**
 * APC(APCu) Cache Safe Using Wrapper
 */
class SafeApc
{
    private $cacheVersionKey = '1';
    private $cacheStartTime = 0;
    private $cacheInternalExpireTime = 86400;

    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new \RuntimeException('APCu is not loaded');
        }
    }

    public function setCacheInternalExpireTime(int $time)
    {
        $this->cacheInternalExpireTime = $time;
    }

    /**
     * must call on initialize!
     * set cache expire calculate start time
     *
     * @param int $time request start time
     */
    public function setCacheStartTime(?int $time = null)
    {
        if ($time === null) {
            $time = time();
        }

        $this->cacheStartTime = $time;
    }

    /**
     * must call on initialize!
     * set cache version key
     *
     * @param string $key version key
     */
    public function setCacheVersionKey(string $key): void
    {
        $this->cache_version_key = $key;
    }

    /**
     * set cache
     *
     * @param string $key    cache key
     * @param mixed  $value  cache value
     * @param int    $expire cache expire time
     * @throws SafeApcException
     */
    public function set(string $key, $value, int $expire = 0): void
    {
        if (!\apcu_store($this->getCacheKey($key), $this->getCacheValue($value, $expire), $this->cacheInternalExpireTime)) {
            throw new SafeApcException('apcu_store failed');
        }
    }

    /**
     * get cache
     *
     * @param  string $key cache key
     * @return mixed       cache value
     * @throws SafeApcNotFoundException
     */
    public function get(string $key)
    {
        $originalValue = \apcu_fetch($this->getCacheKey($key));
        if (!is_array($originalValue)) {
            throw new SafeApcNotFoundException();
        }
        if (!isset($originalValue[0]) || !isset($originalValue[1])) {
            throw new SafeApcNotFoundException();
        }
        list($value, $expire) = $originalValue;
        if ($expire > 0) {
            if ($expire > $this->cacheStartTime) {
                $this->delete($key);
                throw new SafeApcNotFoundException();
            }
        }

        return $value;
    }

    /**
     * delete cache
     *
     * @param string $key cache key
     */
    public function delete(string $key)
    {
        return \apcu_delete($this->getCacheKey($key));
    }

    /**
     * delete cache
     *
     * @param string $key cache key
     */
    public function exists(string $key): bool
    {
        try {
            $this->get($key);
        } catch(SafeApcNotFoundException $e) {
            return false;
        }

        return true;
    }

    protected function getCacheKey(string $key): string
    {
        return sprintf('%s#%s', $this->cacheVersionKey, $key);
    }

    protected function getCacheValue($value, int $expire): array
    {
        $time = 0;
        if ($expire > 0) {
            $time = $this->cacheStartTime + mt_rand(0, 30) + (int)$expire;
        }
        return [$value, $time];
    }
}
