<?php

namespace TechOnline\Utils;



class UrlUtil
{
    public static function buildStaticQuery($map = ['key' => 'k'])
    {
        return new UrlStaticQuery($map);
    }
}

class UrlStaticQuery
{
    public $map = [];
    public $glue = '-';
    private $query = [];

    public function __construct($map = [])
    {
        $this->map = $map;
    }

    public function add($key, $k)
    {
        $this->map[$key] = $k;
    }

    public function create($query)
    {
        $url = [];
        foreach ($query as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (isset($this->map[$k])) {
                if (is_array($v)) {
                    $url[] = $this->map[$k] . $v[0];
                } else {
                    $url[] = $this->map[$k] . urlencode("$v");
                }
            }
        }
        sort($url);
        return join($this->glue, $url);
    }

    public function createMerge($query)
    {
        return $this->create(array_merge($this->query, $query));
    }

    public function parse($queryString)
    {
        $query = [];
        $part = explode($this->glue, $queryString);
        $map = [];
        foreach ($part as $item) {
            foreach ($this->map as $key => $k) {
                if (strpos($item, $k) === 0) {
                    $query[$key] = substr($item, strlen($k));
                }
            }
        }
        $this->query = $query;
        return $query;
    }

    public function parseMergeQuery($query)
    {
        foreach ($query as $k => $v) {
            $this->query[$k] = $v;
        }
    }

    public function getQuery()
    {
        return $this->query;
    }

}