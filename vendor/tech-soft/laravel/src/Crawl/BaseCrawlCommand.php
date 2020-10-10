<?php

namespace TechSoft\Laravel\Crawl;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use TechSoft\Laravel\Crawl\Queue\ArrayQueue;


abstract class BaseCrawlCommand
{
    protected $signature = 'BaseCrawl';
    protected $description = 'BaseCrawl';

    protected $global = [];
    
    protected $queue = null;
    protected $handlers = [];
    protected $onFinishCallable = null;
    protected $delay = [
        'min' => 0,
        'max' => 0,
    ];

    protected function init()
    {
        if (null == $this->queue) {
            $this->queue = new ArrayQueue();
        }
    }

    public function setDelay($minMS, $maxMS)
    {
        $this->delay['min'] = $minMS;
        $this->delay['max'] = $maxMS;
    }

    public function onFinish($callable)
    {
        $this->onFinishCallable = $callable;
    }

    protected function start()
    {
        $this->logInfo("Start");
        $this->logInfo("Use Queue", class_basename($this->queue));
        while (true) {
            $job = $this->queue->poll();
            if (empty($job)) {
                if (null !== $this->onFinishCallable) {
                    call_user_func_array($this->onFinishCallable, [$this]);
                }
                $this->logInfo("End");
                break;
            }
            if (!isset($this->handlers[$job['handler']])) {
                $this->logError("Handler $job[handler] not registered", $job['param']);
                continue;
            }
            $successCallable = $this->handlers[$job['handler']][0];
            $failCallable = $this->handlers[$job['handler']][1];
            $id = $job['id'];
            if ($id === null) {
                if (is_string($job['param']) || is_numeric($job['param'])) {
                    $id = $job['param'];
                } else if (is_array($job['param'])) {
                    $ids = [];
                    foreach ($job['param'] as $k => $v) {
                        if (is_string($v) || is_numeric($v)) {
                        } else {
                            $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                        }
                        $ids[] = "$k:$v";
                    }
                    $id = join(", ", $ids);
                } else {
                    $id = json_encode($job['param'], JSON_UNESCAPED_UNICODE);
                }
            }
            try {
                $successCallable($this, $job['param'], $job['id']);
                $this->logInfo("Execute $job[handler] $id");
            } catch (\Exception $e) {
                $this->logInfo("Execute $job[handler] $id", $e->getMessage());
                if (null !== $failCallable) {
                    try {
                        $failCallable($this, $job['param'], $job['id']);
                    } catch (\Exception $e) {
                        $this->logInfo("Execute Fail for Error Handler $job[handler] $id", $e->getMessage());
                    }
                }
            }
            if ($this->delay['max'] > 0) {
                usleep(rand($this->delay['min'], $this->delay['max']) * 1000);
            }
        }
    }

    public function register($handler, $successCallable, $failCallable = null)
    {
        $this->handlers[$handler] = [
            $successCallable,
            $failCallable,
        ];
    }

    public function logInfo($msg, $data = null)
    {
        $str = [];
        $str[] = date('Y-m-d H:i:s');
        $str[] = '[Crawl]';
        $str[] = $msg;
        if (null != $data) {
            if (is_string($data)) {
                $str[] = $data;
            } else {
                $str[] = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        }
        echo join(' - ', $str) . "\n";
        Log::info(join(' - ', $str));
    }

    public function logError($msg, $data = null)
    {
        $str = [];
        $str[] = date('Y-m-d H:i:s');
        $str[] = '[Crawl]';
        $str[] = $msg;
        if (null != $data) {
            $str[] = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        echo join(' - ', $str) . "\n";
        Log::error(join(' - ', $str));
    }

    public function globalGet($name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->global)) {
            return $this->global[$name];
        }
        return $defaultValue;
    }

    public function globalSet($name, $value)
    {
        $this->global[$name] = $value;
    }

    
    
    
    
    

    abstract public function handle();

}