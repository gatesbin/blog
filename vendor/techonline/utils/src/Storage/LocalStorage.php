<?php

namespace TechOnline\Utils\Storage;


class LocalStorage
{
    static $HASH_SHARD_COUNT = 16;

    private $path;

    public function __construct($path)
    {
        $path = rtrim($path, '/') . '/';
        $this->path = $path;
        @mkdir($this->path, 0755, true);
    }

    public static function build($path)
    {
        return new LocalStorage($path);
    }

    private function wrapPath($path, $prefix = '', $check = true)
    {
        if ($check && !preg_match('/^[a-zA-Z0-9_]+$/', $path)) {
            throw new \Exception('LocalStorage key must match [a-zA-Z0-9]+');
        }
        return $this->path . $prefix . $path;
    }

    public function set($key, $value)
    {
        $path = $this->wrapPath($key) . '.json';
        file_put_contents($path, json_encode($value));
        return $value;
    }

    public function setnx($key, $value)
    {
        if ($this->exists($key)) {
            return;
        }
        $this->set($key, $value);
        return $value;
    }

    public function getOrSetFromResponse($key, $callback)
    {
        if ($this->exists($key)) {
            return $this->get($key);
        }
        $ret = $callback();
        if ($ret['code']) {
            throw new \Exception('getOrSetFromResponse error : ' . $ret['msg']);
        }
        $this->set($key, $ret['data']);
        return $ret['data'];
    }

    public function del($key)
    {
        $path = $this->wrapPath($key) . '.json';
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function get($key)
    {
        $path = $this->wrapPath($key) . '.json';
        if (file_exists($path)) {
            return @json_decode(file_get_contents($path), true);
        }
        return null;
    }

    public function exists($key)
    {
        $path = $this->wrapPath($key) . '.json';
        return file_exists($path);
    }

    public function keys($keyPattern)
    {
        $pathBase = $this->wrapPath('', '', false);
        $path = $this->wrapPath($keyPattern, '', false) . '.json';
        $paths = glob($path);
        return array_map(function ($p) use ($pathBase) {
            return substr(substr($p, strlen($pathBase)), 0, -5);
        }, $paths);
    }

    private function hashPath($key, $hash = null)
    {
        $path = $this->wrapPath($key, 'h.');
        if (null === $hash) {
            $hash = (crc32($key) % self::$HASH_SHARD_COUNT);
        }
        $path = $path . '.' . $hash . '.json';
        return $path;
    }

    public function hset($key, $field, $value)
    {
        $path = $this->hashPath($key);
        $map = [];
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
        }
        $map[$field] = $value;
        file_put_contents($path, json_encode($map));
    }

    public function hget($key, $field)
    {
        $path = $this->hashPath($key);
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
            return array_key_exists($field, $map) ? $map[$field] : null;
        }
        return null;
    }

    public function hgetall($key)
    {
        $path = $this->hashPath($key, '*');
        $paths = glob($path);
        $all = [];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $map = @json_decode(file_get_contents($path), true);
                foreach ($map as $k => $v) {
                    $all[$k] = $v;
                }
            }
        }
        return $all;
    }

    public function hdel($key, $field)
    {
        $path = $this->hashPath($key);
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
            if (array_key_exists($field, $map)) {
                unset($map[$field]);
            }
            file_put_contents($path, json_encode($map));
        }
    }

    public function hexists($key, $field)
    {
        $path = $this->hashPath($key);
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
            return array_key_exists($field, $map);
        }
        return false;
    }

}
