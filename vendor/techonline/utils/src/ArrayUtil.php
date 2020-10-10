<?php

namespace TechOnline\Utils;


class ArrayUtil
{
    public static function sequenceEqual($arr1, $arr2)
    {
        sort($arr1);
        sort($arr2);
        return json_encode($arr1) == json_encode($arr2);
    }

    public static function equal($arr1, $arr2, $keys = null, $strict = false)
    {
        if (null === $keys) {
            $keys = array_merge(array_keys($arr1), array_keys($arr2));
        }
        foreach ($keys as $k) {
            if (!array_key_exists($k, $arr1)) {
                return false;
            }
            if (!array_key_exists($k, $arr2)) {
                return false;
            }
            if ($strict) {
                if ($arr1[$k] !== $arr2[$k]) {
                    return false;
                }
            } else {
                if ($arr1[$k] != $arr2[$k]) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function fetchSpecifiedKeyToArray(&$records, $key)
    {
        $r = [];
        foreach ($records as $item) {
            $r[] = $item[$key];
        }
        return $r;
    }

    public static function filterSpecifiedKey($record, $keys)
    {
        $newArr = [];
        if (empty($keys) || empty($record)) {
            return $newArr;
        }
        foreach ($record as $k => $v) {
            if (in_array($k, $keys)) {
                $newArr[$k] = $v;
            }
        }
        return $newArr;
    }

    public static function exceptSpecifiedKey($record, $keys)
    {
        if (empty($keys) || empty($record)) {
            return $record;
        }
        $newArr = [];
        foreach ($record as $k => $v) {
            if (!in_array($k, $keys)) {
                $newArr[$k] = $v;
            }
        }
        return $newArr;
    }

    public static function filterArraySpecifiedKey(&$records, $keys)
    {
        $newArr = [];
        if (empty($keys)) {
            return $newArr;
        }
        foreach ($records as $v) {
            $item = [];
            foreach ($v as $kk => $vv) {
                if (in_array($kk, $keys)) {
                    $item[$kk] = $vv;
                }
            }
            $newArr[] = $item;
        }
        return $newArr;
    }

    public static function renameArrayKey(&$records, $map)
    {
        foreach ($records as $k => $v) {
            foreach ($map as $old => $new) {
                $records[$k][$new] = $records[$k][$old];
                unset($records[$k][$old]);
            }
        }
    }

    public static function pickRandomOne($records)
    {
        if (empty($records)) {
            return null;
        }
        if (count($records) == 1) {
            return $records[0];
        }
        return $records[array_rand($records)];
    }


    public static function trimAll($records)
    {
        $newArr = [];
        foreach ($records as $k => $v) {
            if (is_array($v)) {
                $newArr[$k] = self::trimAll($v);
            } else {
                $newArr[$k] = trim($v);
            }
        }
        return $newArr;
    }

    public static function isAllEmpty($row)
    {
        if (empty($row) || !is_array($row)) {
            return true;
        }
        for ($i = 0; $i < count($row); $i++) {
            $v = trim($row[$i]);
            if (!empty($v)) {
                return false;
            }
        }
        return true;
    }

    public static function sortByKey($records, $key = 'sort', $sort = 'asc')
    {
        usort($records, function ($o1, $o2) use ($key, $sort) {
            if ($o1[$key] == $o2[$key]) {
                return 0;
            }
            $ret = $o1[$key] > $o2[$key] ? 1 : -1;
            return $sort == 'asc' ? $ret : -$ret;
        });
        return $records;
    }
}