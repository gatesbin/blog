<?php

namespace TechOnline\Laravel\Http;

use TechOnline\Utils\FormatUtil;
use TechOnline\Utils\TimeUtil;
use Illuminate\Support\Facades\Input;

class InputPackage
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    
    public static function buildFromInputJson($key)
    {
        $data = Input::get($key, null);
        $data = @json_decode($data, true);
        return new InputPackage($data);
    }

    
    public static function build($data)
    {
        return new InputPackage($data);
    }

    
    public static function buildFromInput()
    {
        return new InputPackage(Input::all());
    }

    public function all()
    {
        return $this->data;
    }

    public function isEmpty()
    {
        return empty($this->data);
    }

    public function getInteger($key, $defaultValue = 0)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key]) && !is_numeric($this->data[$key])) {
                return $defaultValue;
            }
            return intval($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getPageSize($key = 'pageSize', $min = 10, $max = 100)
    {
        $pageSize = $this->getInteger($key, 1);
        return min(max($pageSize, $min), $max);
    }

    public function getRichContent($key, $defaultValue = 0)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            return trim($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getBoolean($key, $defaultValue = false)
    {
        if (isset($this->data[$key])) {
            if ($this->data[$key]) {
                                if ('false' === $this->data[$key]) {
                    return false;
                }
                return true;
            }
            return false;
        }
        return $defaultValue;
    }

    public function getTrimString($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            return trim($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getMultiTrimString($keys, $defaultValue = '')
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->getTrimString($key, $defaultValue);
        }
        return $values;
    }

    public function getStringSeparatedArray($key, $defaultValue = [], $separated = ',')
    {
        $value = $this->getTrimString($key);
        switch ($separated) {
            case ',':
                $value = str_replace('，', ',', $value);
                break;
        }
        $values = [];
        foreach (explode($separated, $value) as $i) {
            if (empty($i)) {
                continue;
            }
            $values [] = trim($i);
        }
        if (empty($values)) {
            return $defaultValue;
        }
        return $values;
    }

        public function getIdNo($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            return trim($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getEnumValue($enums, $key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $value = trim($this->data[$key]);
            if (in_array($value, $enums)) {
                return $value;
            }
        }
        return $defaultValue;
    }

    public function getPhone($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $phone = trim($this->data[$key]);
            if (!FormatUtil::isPhone($phone)) {
                return null;
            }
            return $phone;
        }
        return $defaultValue;
    }

    public function getTelephone($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $phone = trim($this->data[$key]);
            $phone = FormatUtil::telephone($phone);
            if ($phone) {
                return $phone;
            }
            return null;
        }
        return $defaultValue;
    }

    public function getEmail($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $email = trim($this->data[$key]);
            if (!FormatUtil::isEmail($email)) {
                return null;
            }
            return $email;
        }
        return $defaultValue;
    }

    public function getDecimal($key, $defaultValue = '0.00')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key]) && !is_numeric($this->data[$key])) {
                return $defaultValue;
            }
            if ($this->data[$key] < 0) {
                return $defaultValue;
            }
            $value = intval(bcmul(trim($this->data[$key]), 100, 2));
            $value = bcdiv($value, 100, 2);
            return $value;
        }
        return $defaultValue;
    }

    public function getBase64Image($key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (empty($this->data[$key])) {
                return null;
            }
            $value = $this->data[$key];
            $value = substr($value, strlen('data:image/jpeg;base64,'));
            $value = @base64_decode($value);
            if (empty($value)) {
                return $defaultValue;
            }
            return $value;
        }
        return $defaultValue;
    }

    public function getBase64File($key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (empty($this->data[$key])) {
                return null;
            }
            $value = $this->data[$key];
            $value = @base64_decode($value);
            if (empty($value)) {
                return $defaultValue;
            }
            return $value;
        }
        return $defaultValue;
    }

    public function getDouble($key, $defaultValue = 0)
    {
        if (isset($this->data[$key])) {
            return doubleval($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getImagePath($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            return trim($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getDataUploadedPath($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $value = trim($this->data[$key]);
            if (preg_match('/(data\\/[a-z]+\\/\\d{4}\\/\\d{2}\\/\\d{2}\\/[a-z0-9\\_]+\\.[a-z0-9]+)[\\?]?/', $value, $mat)) {
                return $value;
            }
        }
        return $defaultValue;
    }

    public function getDataUploadedPathArray($key, $defaultValue = [])
    {
        if (isset($this->data[$key])) {
            if (!is_array($this->data[$key])) {
                return $defaultValue;
            }
            $paths = [];
            foreach ($this->data[$key] as $value) {
                $value = trim($value);
                if (preg_match('/(data\\/[a-z]+\\/\\d{4}\\/\\d{2}\\/\\d{2}\\/[a-z0-9\\_]+\\.[a-z0-9]+)[\\?]?/', $value, $mat)) {
                    $paths[] = $value;
                }
            }
            return $paths;
        }
        return $defaultValue;
    }

    public function getDataImagePath($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $value = trim($this->data[$key]);
                        if (preg_match('/(data\\/[a-z]+\\/\\d{4}\\/\\d{2}\\/\\d{2}\\/[a-z0-9\\_]+\\.[a-z0-9]+)[\\?]?/', $value, $mat)) {
                return $value;
            }
            return $value;
        }
        return $defaultValue;
    }

    public function getFilePath($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            return trim($this->data[$key]);
        }
        return $defaultValue;
    }

    public function getColorHex($key, $defaultValue = '')
    {
        if (isset($this->data[$key])) {
            if (preg_match('/^#[0-9a-fA-F]{6}$/', $this->data[$key]) || preg_match('/^#[0-9a-fA-F]{6}$/', $this->data[$key])) {
                return $this->data[$key];
            }
        }
        return $defaultValue;
    }

    public function getImagesPath($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        if (!is_array($this->data[$key])) {
            return $defaultValue;
        }
        $images = $this->data[$key];
        foreach ($images as &$image) {
            if (preg_match('/(data\\/[a-z]+\\/\\d{4}\\/\\d{2}\\/\\d{2}\\/[a-z0-9\\_]+\\.[a-z0-9]+)[\\?]?/', $image, $mat)) {
                $image = $mat[1];
            }
        }
        return $images;
    }

    public function getJsonImagesPath($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        $images = @json_decode($this->data[$key], true);
        if (!is_array($images)) {
            return $defaultValue;
        }
        foreach ($images as &$image) {
            if (preg_match('/(data\\/[a-z]+\\/\\d{4}\\/\\d{2}\\/\\d{2}\\/[a-z0-9\\_]+\\.[a-z0-9]+)[\\?]?/', $image, $mat)) {
                $image = $mat[1];
            }
        }
        return $images;
    }

    public function getJson($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        $data = @json_decode($this->data[$key], true);
        if (empty($data)) {
            return $defaultValue;
        }
        return $data;
    }

    public function getType($key, $typeCls, $defaultValue = null)
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        $data = $this->data[$key];
        if (empty($data)) {
            return $defaultValue;
        }
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($data == $k) {
                return $k;
            }
        }
        return $defaultValue;
    }

    public function getTrimStringArray($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        if (!is_array($this->data[$key])) {
            return $defaultValue;
        }
        $values = [];
        foreach ($this->data[$key] as $item) {
            $values[] = trim($item);
        }
        return $values;
    }

    public function getArray($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        if (!is_array($this->data[$key])) {
            return $defaultValue;
        }
        return $this->data[$key];
    }

    public function getIntegerArray($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        if (!is_array($this->data[$key])) {
            return $defaultValue;
        }
        $arr = [];
        foreach ($this->data[$key] as $v) {
            $arr[] = intval($v);
        }
        return $arr;
    }

    public function getNameValueArray($key, $defaultValue = [])
    {
        if (!isset($this->data[$key])) {
            return $defaultValue;
        }
        if (!is_array($this->data[$key])) {
            return $defaultValue;
        }
        $values = [];
        foreach ($this->data[$key] as $item) {
            $values[] = [
                'name' => $item['name'],
                'value' => $item['value'],
            ];
        }
        return $values;
    }

    public function getDate($key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $date = $this->data[$key];
            if (TimeUtil::isDateEmpty($date)) {
                return $defaultValue;
            }
            return date('Y-m-d', strtotime($date));
        }
        return $defaultValue;
    }

    public function getDatetime($key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $datetime = $this->data[$key];
            if (TimeUtil::isDatetimeEmpty($datetime)) {
                return $defaultValue;
            }
            return date('Y-m-d H:i:s', strtotime($datetime));
        }
        return $defaultValue;
    }

    public function getTime($key, $defaultValue = null)
    {
        if (isset($this->data[$key])) {
            if (!is_string($this->data[$key])) {
                return $defaultValue;
            }
            $time = $this->data[$key];
            if (TimeUtil::isTimeEmpty($time)) {
                return $defaultValue;
            }
            return date('H:i:s', strtotime('2019-01-01 ' . $time));
        }
        return $defaultValue;
    }

    public function getAsInput($key)
    {
        $data = [];
        if (isset($this->data[$key]) && is_array($this->data[$key])) {
            $data = $this->data[$key];
        }
        return self::build($data);
    }

    public function getJsonAsInput($key)
    {
        $data = [];
        if (isset($this->data[$key]) && is_string($this->data[$key])) {
            $data = @json_decode($this->data[$key], true);
        }
        if (empty($data)) {
            $data = [];
        }
        return self::build($data);
    }

    public function hasKey($key)
    {
        return array_key_exists($key, $this->data);
    }

}
