<?php

namespace TechOnline\Utils;


class IDUtil
{
    public static function next64BitId()
    {
        return intval(microtime(true) * 10000) . (getmypid() % 10) . sprintf('%04d', rand(0, 9999));
    }

    public static function next64BitIdSeq($workerId = null)
    {
        static $lastTimestamp = null;
        static $seq = 0;
        static $bit = null;

        do {
            $timestamp = intval(microtime(true) * 10000);
            if ($timestamp !== $lastTimestamp) {
                $seq = 0;
            }
        } while ($seq >= 1000);
        $lastTimestamp = $timestamp;

        if ($workerId === null) {
            $workerId = getmypid();
        }
        $workerBit = ($workerId % 10);

        if (null === $bit) {
            $bit = rand(0, 9);
        }

        return $timestamp . $workerBit . $bit . sprintf('%03d', $seq++);
    }
}
