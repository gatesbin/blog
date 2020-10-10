<?php

namespace TechOnline\Utils\Log;


trait LoggerTrait
{
    private $loggerListenerCallback = null;
    private $loggerListenerBatchSize = 1;
    private $loggerListenerBuffer = [];

    private function logPath($path = null)
    {
        static $pathCurrent = '';
        if (null !== $path) {
            $pathCurrent = rtrim($path) . '/';
        }
        return $pathCurrent;
    }

    private function logListen($callback, $batchSize = 1)
    {
        $this->loggerListenerCallback = $callback;
        $this->loggerListenerBatchSize = $batchSize;
    }

    private function logListenerCheck($force = false)
    {
        if (empty($this->loggerListenerCallback)) {
            return;
        }
        if ($force || count($this->loggerListenerBuffer) >= $this->loggerListenerBatchSize) {
            $callback = $this->loggerListenerCallback;
            if ($this->loggerListenerBatchSize == 1) {
                $callback($this->loggerListenerBuffer[0]);
                $this->loggerListenerBuffer = [];
            } else {
                $callback($this->loggerListenerBuffer);
                $this->loggerListenerBuffer = [];
            }
        }
    }

    private function logFile($file = null)
    {
        static $fileCurrent = 'log';
        if (null !== $file) {
            $fileCurrent = $file;
        }
        return $fileCurrent;
    }

    private function logWrite($type, $label, $msg, $callback = true)
    {
        if (!is_string($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $file = $this->logFile();
        $string = "[" . sprintf('%05d', getmypid()) . "] " . date('Y-m-d H:i:s') . " - $label" . ($msg ? " - $msg" : '');
        @file_put_contents($this->logPath() . "${file}_${type}_" . date('Ymd') . ".log", $string . "\n", FILE_APPEND);
        if ($callback && !empty($this->loggerListenerCallback)) {
            $this->loggerListenerBuffer[] = ['type' => $type, 'text' => $string];
            $this->logListenerCheck();
        }
        return $string;
    }

    private function logInfo($label, $msg = '')
    {
        return $this->logWrite('info', $label, $msg);
    }

    private function logInfoWithoutCallback($label, $msg = '')
    {
        return $this->logWrite('info', $label, $msg, false);
    }

    private function logError($label, $msg = '')
    {
        return $this->logWrite('error', $label, $msg);
    }

    private function logErrorWithoutCallback($label, $msg = '')
    {
        return $this->logWrite('error', $label, $msg);
    }

}
