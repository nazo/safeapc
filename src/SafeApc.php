<?php

namespace SafeApc;

/**
 * APC(APCu) Cache Safe Using Wrapper
 */
class SafeApc
{
    private static $cache_version_key = '1';
    private static $cache_start_time = 0;
    private static $cache_internal_expire_time = 86400;

    public static function setCacheInternalExpireTime($time)
    {
        static::$cache_internal_expire_time = $time;
    }

    /**
     * must call on initialize!
     * set cache expire calculate start time
     *
     * @param int $time request start time
     */
    public static function setCacheStartTime($time = null)
    {
        if ($time === null) {
            $time = time();
        }

        static::$cache_start_time = $time;
    }

    /**
     * must call on initialize!
     * set cache version key
     *
     * @param string $key version key
     */
    public static function setCacheVersionKey($key)
    {
        static::$cache_version_key = (string)$key;
    }

    /**
     * set cache
     *
     * @param string $key    cache key
     * @param mixed  $value  cache value(auto serialized)
     * @param int    $expire cache expire time
     * @throws SafeApcException
     */
    public static function set($key, $value, $expire = 0)
    {
        if (!\apc_store(static::getCacheKey($key), static::getCacheValue($value, $expire), static::$cache_internal_expire_time)) {
            throw new SafeApcException();
        }
    }

    /**
     * get cache
     *
     * @param  string $key cache key
     * @return mixed       cache value
     * @throws SafeApcNotFoundException
     */
    public static function get($key)
    {
        $original_value = \apc_fetch(static::getCacheKey($key));
        if (!is_array($original_value)) {
            throw new SafeApcNotFoundException();
        }
        if (!isset($original_value[0]) || !isset($original_value[1])) {
            throw new SafeApcNotFoundException();
        }
        list($value, $expire) = $original_value;
        if ($expire > 0) {
            if ($expire > static::$cache_start_time) {
                static::delete($key);
                throw new SafeApcNotFoundException();
            }
        }

        return unserialize($value);
    }

    /**
     * delete cache
     *
     * @param string $key cache key
     */
    public static function delete($key)
    {
        return \apc_delete(static::getCacheKey($key));
    }

    /**
     * delete cache
     *
     * @param string $key cache key
     */
    public static function exists($key)
    {
        try {
            static::get($key);
        } catch(SafeApcNotFoundException $e) {
            return false;
        }

        return true;
    }

    protected static function getCacheKey($key)
    {
        return sprintf('%s#%s', static::$cache_version_key, $key);
    }

    protected static function getCacheValue($value, $expire)
    {
        $time = 0;
        if ($expire > 0) {
            $time = static::$cache_start_time + rand(0, 30) + (int)$expire;
        }
        return array(serialize($value), $time);
    }
}
