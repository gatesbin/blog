<?php

namespace TechOnline\Laravel\Util;

use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use TechOnline\Laravel\Http\Request;
use TechSoft\Laravel\Assets\AssetsUtil;

class MarkdownUtils
{
    public static function replaceImageSrcToCDN($content, $dataAttr = 'data-src')
    {
        $currentDomainUrl = Request::domainUrl();
        preg_match_all('/!\\[(.*?)\\]\\((.*?)\\)/i', $content, $mat);
        foreach ($mat[0] as $k => $v) {
            $imageUrl = $mat[2][$k];
            if (Str::startsWith($imageUrl, $currentDomainUrl)) {
                $imageUrl = substr($mat[2][$k], strlen($currentDomainUrl));
                $content = str_replace($v, '![' . $mat[1][$k] . '](' . AssetsUtil::fix($imageUrl) . ')', $content);
            }
        }
        return $content;
    }

}