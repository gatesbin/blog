<?php

namespace TechOnline\Utils\Log;

interface Logger
{
    const INFO = 'info';
    const ERROR = 'error';

    public function log($level, $label, $info);

    public function listen($batchSize, $callback);

    public function listenCheck($force = false);
}