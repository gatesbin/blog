<?php

namespace TechOnline\Utils\Log;

class FileLogger implements Logger
{
    private $listener = null;
    private $listenerBuffer = [];

    private $file;

    public function __construct($file)
    {
        $this->file = $file;
        $dir = dirname($this->file);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    public function log($level, $label, $msg)
    {
        $msg = date('Y-m-d H:i:s') . " [$level] - $label - $msg\n";
        file_put_contents($this->file, $msg, FILE_APPEND);
        $this->listenerBuffer[] = $msg;
    }

    public function listen($batchSize, $callback)
    {
        $this->listener = ['batchSize' => $batchSize, 'callback' => $callback];
    }

    public function listenCheck($force = false)
    {
        if ($force || count($this->listenerBuffer) >= $this->$listener['batchSize']) {
            $this->listener['callback']($this->listenerBuffer);
            $this->listenerBuffer = [];
        }
    }


}