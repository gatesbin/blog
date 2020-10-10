<?php

namespace TechSoft\Laravel\View;

use Illuminate\Support\Facades\Blade;

trait BladeEnhanceTrait
{
    public function bootBladeEnhance()
    {
        Blade::directive('jsonString', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+),(.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                $default = trim($mat[2]);
                return "<" . "?php echo json_encode(empty($var)?$default:$var); ?" . ">";
            } else if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo json_encode(empty($var)?'':$var); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonBoolean', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo json_encode(empty($var)?false:true); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonDatetime', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);

                return "<" . "?php echo json_encode(empty($var) || \\TechOnline\\Utils\\TimeUtil::isDatetimeEmpty($var)?'':$var); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonDate', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo json_encode(empty($var) || \\TechOnline\\Utils\\TimeUtil::isDateEmpty($var)?'':$var); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonNumber', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo json_encode(intval(empty($var)?0:$var)); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonArray', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo json_encode(empty($var)?[]:$var); ?" . ">";
            } else {
                return '';
            }
        });
        Blade::directive('jsonObject', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $var = trim($mat[1]);
                return "<" . "?php echo empty($var)?'{}':json_encode($var); ?" . ">";
            } else {
                return '';
            }
        });

    }
}