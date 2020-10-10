<?php

namespace TechSoft\Laravel\Data;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Util\FileUtils;
use TechOnline\Utils\FileUtil;
use Illuminate\Support\Str;
use TechSoft\Laravel\Assets\AssetsUtil;
use TechSoft\Laravel\Config\ConfigUtil;

class DataUtil
{

    const DATA_TEMP = 'data_temp';
    const DATA = 'data';
    const DATA_CHUNK = 'data_chunk';

    const PATTERN_DATA_TEMP = '/^data_temp\\/([a-z_]+)\\/([a-zA-Z0-9]{32}\\.[a-z0-9]+)$/';
    const PATTERN_DATA = '/^data\\/([a-z_]+)\\/(\\d+\\/\\d+\\/\\d+\\/\\d+_[a-zA-Z0-9]{4}_\\d+\\.[a-z0-9]+)$/';

    const PATTERN_DATA_STRING = 'data\\/([a-z_]+)\\/(\\d+\\/\\d+\\/\\d+\\/\\d+_[a-zA-Z0-9]{4}_\\d+\\.[a-z0-9]+)';

    static $UPLOAD_TIMESTAMP = null;

    private static $dataStorageService;
    private static $config;

    
    
        public static function storeContentTempPath($content)
    {
        if (!$content) {
            return $content;
        }
        preg_match_all('/(data_temp\\/([a-z_]+)\\/([a-zA-Z0-9]{32}\\.[a-z0-9]+))("|\')/', $content, $mat);
        $pathMap = [];

        if (!empty($mat[1])) {
            foreach ($mat[1] as $tempPath) {
                $pathMap[$tempPath] = '';
            }
        }
        if (!empty($pathMap)) {
            foreach ($pathMap as $tempPath => $empty) {
                $ret = self::storeTempDataByPath($tempPath);
                if (!$ret['code']) {
                    $pathMap[$tempPath] = $ret['data']['path'];
                }
            }
            foreach ($pathMap as $tempPath => $path) {
                $content = str_replace($tempPath, $path, $content);
            }
        }
        return $content;
    }


    public static function uploadToTempData($category, $filename, $content, $option = [])
    {
        $option = self::prepareOption($option);

        if (empty(self::$config[$category])) {
            return Response::generate(-1, '未知的分类:' . $category);
        }
        $config = self::$config[$category];

        if (strlen($filename) > 200) {
            return Response::generate(-2, '文件名太长，最多200字节');
        }

        $extension = FileUtil::extension($filename);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-3, '不允许的文件类型:' . $extension);
        }

        $size = strlen($content);
        if ($size > $config['maxSize']) {
            return Response::generate(-4, '文件大小超过上限:' . FileUtil::formatByte($config['maxSize']));
        }

        $retry = 0;
        do {
            $path = strtolower(Str::random(32)) . '.' . $extension;
            $fullPath = self::DATA_TEMP . '/' . $category . '/' . $path;
        } while ($retry++ < 10 && self::$dataStorageService->exists($fullPath));
        if ($retry >= 10) {
            return Response::generate(-5, '上传失败，创建临时文件次数超时');
        }

        self::$dataStorageService->put($fullPath, $content);

        $m = ModelUtil::insert('data_temp', [
            'category' => $category,
            'path' => $path,
            'filename' => $filename,
            'size' => $size,
        ]);

        return Response::generate(0, 'ok', $m);
    }

    public static function storeTempDataByPath($tempPath, $option = [])
    {
        $option = self::prepareOption($option);

        $tempPath = trim($tempPath, '/');
        if (preg_match(self::PATTERN_DATA_TEMP, $tempPath, $mat)) {
            return self::storeTempData($mat[1], $mat[2], $option);
        }
        return Response::generate(-1, '错误的临时文件路径', null);
    }

    public static function storeTempData($category, $tempDataPath, $option = [])
    {
        $option = self::prepareOption($option);

        $dataTemp = ModelUtil::get('data_temp', ['category' => $category, 'path' => $tempDataPath]);
        if (empty($dataTemp)) {
            return Response::generate(-1, '临时文件不存在');
        }

        $extension = FileUtil::extension($dataTemp['filename']);

        $updateTimestamp = time();
        if (self::$UPLOAD_TIMESTAMP) {
            $updateTimestamp = self::$UPLOAD_TIMESTAMP;
        }

        $path = date('Y/m/d/', $updateTimestamp) . (time() % 86400) . '_' . strtolower(Str::random(4)) . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $fullPath = self::DATA . '/' . $category . '/' . $path;

        $from = self::DATA_TEMP . '/' . $dataTemp['category'] . '/' . $dataTemp['path'];
        $to = self::DATA . '/' . $dataTemp['category'] . '/' . $path;

        if (!self::$dataStorageService->exists($from)) {
            ModelUtil::delete('data_temp', ['id' => $dataTemp['id']]);
            return Response::generate(-3, '临时文件不存在');
        }

        $func = config('data.upload.' . $category . '.formatChecker');
        if ($func) {
            $fullPath = self::getTempFullPath($from);
            $pcs = explode('::', $func);
            if (count($pcs) == 1) {
                $ret = $func($fullPath);
            } else if (count($pcs) == 2) {
                $cls = $pcs[0];
                $func = $pcs[1];
                $ret = $cls::$func($fullPath);
            } else {
                throw new \Exception('formatChecker error : ' . $func);
            }
            if ($ret['code']) {
                return Response::generate(-1, $ret['msg']);
            }
        }

        self::$dataStorageService->move($from, $to);

        $data = ModelUtil::insert('data', [
            'category' => $dataTemp['category'],
            'path' => $path,
            'filename' => $dataTemp['filename'],
            'size' => $dataTemp['size'],
        ]);

        switch ($option['driver']) {
            case 'ossAliyun':
                ModelUtil::update('data', ['id' => $data['id']], [
                    'driver' => $option['driver'],
                    'domain' => $option['domain'],
                ]);
                $data['driver'] = $option['driver'];
                $data['domain'] = $option['domain'];
                break;
        }

        ModelUtil::delete('data_temp', ['id' => $dataTemp['id']]);

        return Response::generate(0, 'ok', [
            'data' => $data,
            'path' => self::DATA . '/' . $data['category'] . '/' . $data['path']
        ]);
    }

    
    public static function deleteById($id, $option = [])
    {
        $data = ModelUtil::get('data', ['id' => $id]);
        if (empty($data)) {
            return;
        }
        $option = self::prepareOption($option);
        $file = self::DATA . '/' . $data['category'] . '/' . $data['path'];
        self::$dataStorageService->delete($file);
        ModelUtil::delete('data', ['id' => $id]);
    }


    public static function isTempDataPath($path)
    {
        return preg_match(self::PATTERN_DATA_TEMP, $path);
    }

    public static function deleteRaw($path)
    {
        $option = self::prepareOption(self::getOSSOption());
        self::$dataStorageService->delete(self::DATA . '/' . $path);
    }

    public static function putRaw($category, $path, $content)
    {
        $option = self::prepareOption(self::getOSSOption());
        if (empty(self::$config[$category])) {
            return Response::generate(-1, '未知的分类:' . $category);
        }
        $config = self::$config[$category];

        $extension = FileUtil::extension($path);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, '不允许的文件类型:' . $extension);
        }

        $size = strlen($content);
        if ($size == 0) {
            return Response::generate(-5, '上传文件为空');
        }
        if ($size > $config['maxSize']) {
            return Response::generate(-6, '文件大小超过上限:' . FileUtil::formatByte($config['maxSize']));
        }

        $fullPath = self::DATA . '/' . $path;
        self::$dataStorageService->put($fullPath, $content);
        return Response::generate(0, 'ok', [
            'path' => $fullPath,
        ]);
    }

    public static function upload($category, $filename, $content)
    {
        $option = self::prepareOption(self::getOSSOption());

        if (empty(self::$config[$category])) {
            return Response::generate(-1, '未知的分类:' . $category);
        }
        $config = self::$config[$category];

        if (empty($filename)) {
            return Response::generate(-2, '文件名为空');
        }
        if (strlen($filename) > 200) {
            return Response::generate(-3, '文件名太长，最多200字节');
        }

        $extension = FileUtil::extension($filename);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, '不允许的文件类型:' . $extension);
        }

        $size = strlen($content);
        if ($size == 0) {
            return Response::generate(-5, '上传文件为空');
        }
        if ($size > $config['maxSize']) {
            return Response::generate(-6, '文件大小超过上限:' . FileUtil::formatByte($config['maxSize']));
        }

        $updateTimestamp = time();
        if (self::$UPLOAD_TIMESTAMP) {
            $updateTimestamp = self::$UPLOAD_TIMESTAMP;
        }

        $retry = 0;
        do {
            $path = date('Y/m/d/', $updateTimestamp) . (time() % 86400) . '_' . strtolower(Str::random(4)) . '_' . mt_rand(1000, 9999) . '.' . $extension;
            $fullPath = self::DATA . '/' . $category . '/' . $path;
        } while ($retry++ < 10 && self::$dataStorageService->exists($fullPath));
        if ($retry >= 10) {
            return Response::generate(-7, '上传失败，创建文件次数超时');
        }

        self::$dataStorageService->put($fullPath, $content);

        $data = ModelUtil::insert('data', [
            'category' => $category,
            'path' => $path,
            'filename' => $filename,
            'size' => $size,
        ]);

        switch ($option['driver']) {
            case 'ossAliyun':
                ModelUtil::update('data', ['id' => $data['id']], [
                    'driver' => $option['driver'],
                    'domain' => $option['domain'],
                ]);
                $data['driver'] = $option['driver'];
                $data['domain'] = $option['domain'];
                break;
        }

        $path = '/' . DataUtil::DATA . '/' . $data['category'] . '/' . $data['path'];
        $fullPath = $path;
        if (!empty($data['domain'])) {
            $fullPath = $data['domain'] . $path;
        }

        return Response::generate(0, 'ok', [
            'data' => $data,
            'path' => $path,
            'fullPath' => $fullPath,
        ]);
    }

    public static function uploadHandle($category, $input, $extra = [], $option = [])
    {
        $option = self::prepareOption($option);

        $action = empty($input['action']) ? '' : $input['action'];

        $file = [];
        foreach (['name', 'type', 'lastModifiedDate', 'size'] as $k) {
            if (empty($input[$k])) {
                return Response::generate(-1, $k . '为空');
            }
            $file[$k] = $input[$k] . '';
        }
        $file = array_merge($file, $extra);

        if (empty(self::$config[$category])) {
            return Response::generate(-2, '未知的分类:' . $category);
        }
        $config = self::$config[$category];

        if (strlen($file['name']) > 200) {
            return Response::generate(-3, '文件名太长，最多200字节');
        }

        $extension = FileUtil::extension($file['name']);
        if (!in_array($extension, $config['extensions'])) {
            return Response::generate(-4, '不允许的文件类型:' . $extension);
        }

        if ($file['size'] > $config['maxSize']) {
            return Response::generate(-5, '文件大小超过上限:' . FileUtil::formatByte($config['maxSize']));
        }

        switch ($action) {

            case 'init':
                return self::$dataStorageService->multiPartInit([
                    'category' => $category,
                    'file' => $file,
                ]);

            default:
                return self::$dataStorageService->multiPartUpload([
                    'category' => $category,
                    'file' => $file,
                    'input' => $input,
                ]);
        }

    }

    public static function getTempFullPath($path)
    {
        $option = self::getOSSOption();
        switch ($option['driver']) {
            case 'ossAliyun':
                return $option['domain'] . '/' . $path;
        }
        return '/' . $path;
    }

    public static function prepareTempDataForLocalUse($tempDataPath)
    {
        $fileFullPath = self::getTempFullPath($tempDataPath);
        $localFile = FileUtils::savePathToLocal($fileFullPath, '.' . FileUtil::extension($tempDataPath));
        if (!file_exists($localFile)) {
            return Response::generate(-1, '保存文件出错');
        }
        return Response::generate(0, null, [
            'path' => $localFile
        ]);
    }

    public static function getOSSOption()
    {
        static $option = null;
        if (null === $option) {
            $option = [];
            $option['driver'] = ConfigUtil::getWithEnv('uploadDriver', '');
            $option['domain'] = ConfigUtil::getWithEnv('uploadDriverDomain', '');
            switch ($option['driver']) {
                case 'ossAliyun':
                    $option['aliyunAccessKeyId'] = ConfigUtil::getWithEnv('uploadDriverAliyunAccessKeyId', '');
                    $option['aliyunAccessKeySecret'] = ConfigUtil::getWithEnv('uploadDriverAliyunAccessKeySecret', '');
                    $option['aliyunEndpoint'] = ConfigUtil::getWithEnv('uploadDriverAliyunEndpoint', '');
                    $option['aliyunBucket'] = ConfigUtil::getWithEnv('uploadDriverAliyunBucket', '');
                    break;
            }
        }
        return $option;
    }

    private static function prepareOption($option)
    {
        static $init = false;
        if ($init) {
            return $option;
        }
        $init = true;
        if (empty($option['driver'])) {
            $option['driver'] = $option;
        }
        self::$config = config('data.upload', []);
        switch ($option['driver']) {
            case 'ossAliyun':
                self::$dataStorageService = app(DataOSSAliyunStorageService::class);
                self::$dataStorageService->init($option);
                break;
            default:
                self::$dataStorageService = app(DataStorageService::class);
                break;
        }
        return $option;
    }

    public static function prepare()
    {
        self::prepareOption(self::getOSSOption());
    }

    public static function fix($path)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        return AssetsUtil::fix(self::getTempFullPath($path));
    }

    public static function fixFull($path)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        return AssetsUtil::fixFull(self::getTempFullPath($path));
    }

    public static function repairContent($content)
    {
        $urls = [];
        preg_match_all('/\\((\\/?' . self::PATTERN_DATA_STRING . ')\\)/', $content, $mat);
        if (!empty($mat[1])) {
            $urls = array_merge($urls, $mat[1]);
        }
        preg_match_all('/"(\\/?' . self::PATTERN_DATA_STRING . ')"/', $content, $mat);
        if (!empty($mat[1])) {
            $urls = array_merge($urls, $mat[1]);
        }
        if (empty($urls)) {
            return $content;
        }
        $map = array_build($urls, function ($k, $o) {
            return [$o, self::fix($o)];
        });
        $searchs = [];
        $replaces = [];
        foreach ($map as $old => $new) {
            $searchs[] = $old;
            $replaces[] = $new;
        }
        return str_replace($searchs, $replaces, $content);
    }

    public static function repairJsonArray($json)
    {
        $arr = @json_decode($json, true);
        if (empty($arr)) {
            return $json;
        }
        foreach ($arr as $i => $v) {
            $arr[$i] = self::fix($v);
        }
        return json_encode($arr);
    }

}
