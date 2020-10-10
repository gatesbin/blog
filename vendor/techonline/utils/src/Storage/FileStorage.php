<?php

namespace TechOnline\Utils\Storage;

class FileStorage implements Storage
{
    private $path;

    public function __construct($path)
    {
        $path = rtrim($path, '/') . '/';
        $this->path = $path;
        @mkdir($this->path, 0755, true);
    }

    public function mapListGroups($path)
    {
        $path = $this->path . 'map.' . $path . '.*.json';
        $paths = glob($path);
        return array_map(function ($p) {
            return substr(substr($p, strlen($this->path) + 4), 0, -5);
        }, $paths);
    }

    public function mapListGroupData($mapGroup)
    {
        $path = $this->path . 'map.' . $mapGroup . '.json';
        $map = null;
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
        }
        if (empty($map)) {
            return [];
        }
        return $map;
    }

    public function mapPut($group, $key, $value)
    {
        $hash = substr(md5($key), 0, 1);
        $path = $this->path . 'map.' . $group . '.' . $hash . '.json';
        $map = [];
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
        }
        $map[$key] = $value;
        file_put_contents($path, json_encode($map));
    }

    public function mapGet($group, $key)
    {
        $hash = substr(md5($key), 0, 1);
        $path = $this->path . 'map.' . $group . '.' . $hash . '.json';
        if (file_exists($path)) {
            $map = @json_decode(file_get_contents($path), true);
            return isset($map[$key]) ? $map[$key] : null;
        }
        return null;
    }

    public function mapHas($group, $key)
    {
        return $this->mapGet($group, $key) !== null;
    }

    public function exists($path)
    {
        return file_exists($this->path . $path . '.json');
    }

    public function put($path, $data)
    {
        file_put_contents($this->path . $path . '.json', json_encode($data));
    }

    public function get($path)
    {
        $path = $this->path . $path . '.json';
        if (file_exists($path)) {
            return @json_decode(file_get_contents($path), true);
        }
        return null;
    }

    public function getOrCreateFromResponse($path, $callback)
    {
        $path = $this->path . $path . '.json';
        if (file_exists($path)) {
            return @json_decode(file_get_contents($path), true);
        }
        $ret = $callback();
        file_put_contents($path, json_encode($ret['data']));
        return $ret['data'];
    }

    public function listPattern($path)
    {
        $path = $this->path . $path . '.json';
        $paths = glob($path);
        return array_map(function ($p) {
            return substr(substr($p, strlen($this->path)), 0, -5);
        }, $paths);
    }


}