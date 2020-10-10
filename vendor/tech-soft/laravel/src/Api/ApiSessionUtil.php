<?php

namespace TechSoft\Laravel\Api;


use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Redis\RedisUtil;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Request;
use Illuminate\Support\Str;
use TechOnline\Laravel\Util\AtomicUtil;

class ApiSessionUtil
{
    const DATA_MAX_LENGTH = 2000;
    const EXPIRE_SECONDS = 2592000;
    const TOKEN_LENGTH = 64;

    const TYPE_NONE = 'none';
    const TYPE_DB = 'db';
    const TYPE_REDIS = 'redis';
    const TYPE_SESSION = 'session';

    static $type = 'session';

    public static function setType($type)
    {
        self::$type = $type;
    }

    private static $tokenDataCache = [];

    public static function getToken()
    {
        return Request::headerGet('api-token', null);
    }

    public static function getOrGenerateToken()
    {
        $token = Request::headerGet('api-token', null);
        if (empty($token)) {
            $token = Str::random(self::TOKEN_LENGTH);
            Request::headerSet('api-token', $token);
        }
        return $token;
    }

    public static function all($token = null, $default = [])
    {
        if (self::$type == self::TYPE_SESSION) {
            return Session::all();
        }
        if (empty($token)) {
            $token = Request::headerGet('api-token');
        }
        if (empty($token)) {
            return $default;
        }
        if (!isset(self::$tokenDataCache[$token])) {
            switch (self::$type) {
                case self::TYPE_NONE:
                    return $default;
                case self::TYPE_REDIS:
                    $key = 'api_token:' . $token;
                    $data = RedisUtil::getObject($key);
                    if (empty($data)) {
                        $data = $default;
                    }
                    self::$tokenDataCache[$token] = $data;
                    RedisUtil::expire($key, self::EXPIRE_SECONDS);
                    break;
                case self::TYPE_DB:
                    $m = ModelUtil::get('api_token', ['token' => $token]);
                    if (empty($m)) {
                        self::$tokenDataCache[$token] = [];
                        return $default;
                    }
                    if (strtotime($m['expireTime']) < time()) {
                        ModelUtil::delete('api_token', ['token' => $token]);
                        self::$tokenDataCache[$token] = [];
                        return $default;
                    } else {
                        $data = @json_decode($m['data'], true);
                        if (empty($data)) {
                            $data = $default;
                        }
                        self::$tokenDataCache[$token] = $data;
                        $update = [];
                        $update['expireTime'] = Carbon::now()->addSeconds(self::EXPIRE_SECONDS);
                        ModelUtil::update('api_token', ['id' => $m['id']], $update);
                    }
                    break;
            }
        }
        return self::$tokenDataCache[$token];
    }

    public static function get($name, $defaultValue = null, $token = null)
    {
        $all = self::all($token);
        if (isset($all[$name])) {
            return $all[$name];
        }
        return $defaultValue;
    }

    public static function put($name, $value, $token = null)
    {
        if (self::$type == self::TYPE_SESSION) {
            if (null === $value) {
                Session::forget($name);
            } else {
                Session::put($name, $value);
            }
            return true;
        }
        if (empty($token)) {
            $token = self::getOrGenerateToken();
        }

        switch (self::$type) {
            case self::TYPE_NONE:
                return true;
            case self::TYPE_REDIS:
                $key = 'api_token:' . $token;
                $m = RedisUtil::getObject($key);
                if (empty($m)) {
                    RedisUtil::setexObject($key, [], self::EXPIRE_SECONDS);
                    $m = [];
                }
                if (!isset(self::$tokenDataCache[$token])) {
                    self::$tokenDataCache[$token] = $m;
                }
                self::$tokenDataCache[$token][$name] = $value;
                if (null === $value) {
                    unset(self::$tokenDataCache[$token][$name]);
                }
                if (empty(self::$tokenDataCache[$token])) {
                    RedisUtil::delete($key);
                }
                RedisUtil::setexObject($key, self::$tokenDataCache[$token], self::EXPIRE_SECONDS);
                return true;
            case self::TYPE_DB:
                $m = ModelUtil::get('api_token', ['token' => $token]);
                if (empty($m)) {
                    $m = ModelUtil::insert('api_token', ['token' => $token, 'data' => json_encode([])]);
                }

                if (!isset(self::$tokenDataCache[$token])) {
                    self::$tokenDataCache[$token] = @json_decode($m['data'], true);
                }
                self::$tokenDataCache[$token][$name] = $value;
                if (null === $value) {
                    unset(self::$tokenDataCache[$token][$name]);
                }
                if (empty(self::$tokenDataCache[$token])) {
                    ModelUtil::delete('api_token', ['id' => $m['id']]);
                    return true;
                }
                $dataJson = json_encode(self::$tokenDataCache[$token]);
                if (strlen($dataJson) > self::DATA_MAX_LENGTH) {
                    throw new \Exception('ApiSessionService.LengthOversize -> ' . $dataJson);
                    return false;
                }
                $update = [];
                $update['data'] = $dataJson;
                $update['expireTime'] = Carbon::now()->addSeconds(self::EXPIRE_SECONDS);
                ModelUtil::update('api_token', ['id' => $m['id']], $update);
                return true;
        }
        return false;
    }

    public static function forget($name, $token = null)
    {
        if (self::$type == self::TYPE_SESSION) {
            Session::forget($name);
            return true;
        }
        if (empty($token)) {
            $token = Request::headerGet('api-token');
        }
        if (empty($token)) {
            return true;
        }
        self::put($name, null, $token);
    }

    public static function atomicProduce($name, $value, $expire = 3600, $token = null)
    {
        if (self::$type == self::TYPE_SESSION) {
            return AtomicUtil::produce("$name:" . Session::getId(), $value, $expire);
        }
        if (empty($token)) {
            $token = Request::headerGet('api-token');
        }
        if (empty($token)) {
            return false;
        }
        return AtomicUtil::produce("$name:$token", $value, $expire);
    }

    public static function atomicConsume($name, $token = null)
    {
        if (self::$type == self::TYPE_SESSION) {
            return AtomicUtil::consume("$name:" . Session::getId());
        }
        if (empty($token)) {
            $token = Request::headerGet('api-token');
        }
        if (empty($token)) {
            return false;
        }
        return AtomicUtil::consume("$name:$token");
    }

    public static function atomicRemove($name, $token = null)
    {
        if (self::$type == self::TYPE_SESSION) {
            return AtomicUtil::remove("$name:" . Session::getId());
        }
        if (empty($token)) {
            $token = Request::headerGet('api-token');
        }
        if (empty($token)) {
            return false;
        }
        return AtomicUtil::remove("$name:$token");
    }
}
