<?php

namespace TechSoft\Laravel\Data;

use TechOnline\Utils\FileUtil;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class AbstractDataStorageService
{
    protected $storage;
    protected $option = [];

    function __construct()
    {
        config(['filesystems.disks.data' => [
            'driver' => 'local',
            'root' => base_path('public/')
        ]]);
        $this->storage = Storage::disk('data');
    }

    public function get($filename)
    {
        if ($this->storage->has($filename)) {
            return $this->storage->get($filename);
        }
        return null;
    }

    public function size($filename)
    {
        return $this->storage->size($filename);
    }

    protected function multiPartInitToken($param)
    {
        $category = $param['category'];
        $file = $param['file'];
        ksort($file, SORT_STRING);
        $hash = md5(serialize($file));
        $hashFile = DataUtil::DATA_CHUNK . '/token/' . $hash . '.php';
        if (file_exists($hashFile)) {
            $file = (include $hashFile);
        } else {
            $file['chunkUploaded'] = 0;
            $file['hash'] = $hash;

                        $extension = FileUtil::extension($file['name']);
            $file['path'] = strtolower(Str::random(32)) . '.' . $extension;
            $file['fullPath'] = DataUtil::DATA_TEMP . '/' . $category . '/' . $file['path'];

        }
        return $file;
    }

    protected function uploadChunkTokenAndDeleteToken($token)
    {
        $hash = $token['hash'];
        $hashFile = DataUtil::DATA_CHUNK . '/token/' . $hash . '.php';
        $this->storage->delete($hashFile);
    }

    protected function uploadChunkTokenAndUpdateToken($token)
    {
        $hash = $token['hash'];
        $hashFile = DataUtil::DATA_CHUNK . '/token/' . $hash . '.php';
        $this->storage->put($hashFile, '<' . '?php return ' . var_export($token, true) . ';');
    }

    public function init($option)
    {
        $this->option = $option;
    }

    public function multiPartInit($param)
    {
        throw new \Exception('you should overwrite multiPartInit function');
    }

    public function multiPartUpload($param)
    {
        throw new \Exception('you should overwrite multiPartUpload function');
    }

    public function exists($filename)
    {
        throw new \Exception('you should overwrite exists function');
    }

    public function move($from, $to)
    {
        throw new \Exception('you should overwrite move function');
    }

    public function delete($filename)
    {
        throw new \Exception('you should overwrite move function');
    }

    public function put($filename, $content)
    {
        throw new \Exception('you should overwrite put function');
    }

}
