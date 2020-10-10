<?php

namespace TechSoft\Laravel\Lock;

use NinjaMutex\Lock\MySqlLock;
use NinjaMutex\MutexFabric;

class DBLockUtil
{
    static $instance = null;

    
    private static function instance()
    {
        if (null === self::$instance) {
            $mysqlLock = new MySqlLock(
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_HOST')
            );
            $mutexFabric = new MutexFabric('mysql', $mysqlLock);
            self::$instance = $mutexFabric;
        }
        return self::$instance;
    }

    public static function acquire($name, $timeout = null)
    {
        if (self::instance()->get($name)->acquireLock($timeout)) {
            return true;
        }
        return false;
    }

    public static function release($name)
    {
        self::instance()->get($name)->releaseLock();
    }
}