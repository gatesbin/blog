<?php

namespace TechSoft\Laravel\Html;

use TechSoft\Laravel\Util\HtmlUtil;

class HtmlConverter
{
    public static function convertToHtml($contentType,
                                         $content,
                                         $interceptors = null)
    {
        switch ($contentType) {
            case HtmlType::RICH_TEXT:
                $html = HtmlUtil::filter2($content);
                break;
            case HtmlType::MARKDOWN:
                $parsedown = new \Parsedown();
                $html = $parsedown->setBreaksEnabled(true)->text($content);
                $html = HtmlUtil::filter($html);
                break;
            case HtmlType::SIMPLE_TEXT:
                $html = HtmlUtil::text2html($content);
                break;
            default:
                throw new \Exception('HtmlConverter.convertToHtml contentType error');
        }
        if (!empty($interceptors)) {
            if (is_array($interceptors)) {
                foreach ($interceptors as $interceptor) {
                    $ins = new $interceptor();
                    $html = $ins->convert($html);
                }
            } else {
                $ins = new $interceptors();
                $html = $ins->convert($html);
            }

        }
        return $html;
    }
}