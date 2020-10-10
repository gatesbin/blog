<?php

namespace TechSoft\Laravel\Util;

use Illuminate\Support\Str;
use TechSoft\Laravel\Assets\AssetsUtil;
use TechSoft\Laravel\Data\DataUtil;

class HtmlUtil
{
    public static function replaceImageSrcToLazyLoad($content, $dataAttr = 'data-src', $useAssets = false)
    {
        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);
        if ($useAssets) {
            foreach ($mat[0] as $k => $v) {
                $content = str_replace($v, $mat[1][$k] . $dataAttr . '="' . DataUtil::fix($mat[2][$k]) . '"' . $mat[3][$k], $content);
            }
        } else {
            foreach ($mat[0] as $k => $v) {
                $content = str_replace($v, $mat[1][$k] . $dataAttr . '="' . DataUtil::fix($mat[2][$k]) . '"' . $mat[3][$k], $content);
            }
        }
        return $content;
    }

    public static function replaceImageSrcToFull($content, $useAssets = false)
    {
        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);
        foreach ($mat[0] as $k => $v) {
            $content = str_replace($v, $mat[1][$k] . 'src="' . DataUtil::fixFull($mat[2][$k]) . '"' . $mat[3][$k], $content);
        }
        return $content;
    }

    public static function extractTextAndImages($content)
    {
        $summary = [
            'text' => '',
            'images' => []
        ];

        $text = preg_replace('/<[^>]+>/', '', $content);
        $summary['text'] = $text;

        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1])) {
            $summary['images'] = $mat[1];
        }

        return $summary;
    }

    public static function cover($content)
    {
        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1][0])) {
            return $mat[1][0];
        }
        return null;
    }

    public static function text($content, $limit = null)
    {
        $text = preg_replace('/<[^>]+>/', '', $content);
        if (null !== $limit) {
            $text = Str::limit($text, $limit);
        }
        return $text;
    }

    public static function filter($content)
    {
        return clean($content, [
            'HTML.Allowed' => 'b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1,h2,h3,h4,h5,blockquote',
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'CSS.MaxImgLength' => null,
        ]);
    }

    public static function filter2($content)
    {
        preg_match_all('/<iframe.*?<\\/iframe>/i', $content, $mat1);
        preg_match_all('/<audio.*?<\\/audio>/i', $content, $mat2);
        $replaces = [
            'search' => [],
            'replace' => [],
        ];
        $index = 0;
        foreach (array_merge($mat1[0], $mat2[0]) as $v) {
            $replaces['search'][] = $v;
            $replaces['replace'][] = '--iframe--' . ($index++) . '--';
        }
        $content = str_replace($replaces['search'], $replaces['replace'], $content);

        $content = clean($content, [
            'HTML.Allowed' => 'b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1,h2,h3,h4,h5,blockquote',
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'CSS.MaxImgLength' => null,
        ]);

        return str_replace($replaces['replace'], $replaces['search'], $content);
    }

    
    public static function text2html($text, $htmlspecialchars = true)
    {
        if (empty($text)) {
            return '';
        }
        if ($htmlspecialchars) {
            $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
        }
        $text = str_replace("\r", '', $text);
        $text = str_replace("\n", '</p><p>', $text);
        $text = str_replace('<p></p>', '<p>&nbsp;</p>', $text);
        return '<p>' . $text . '</p>';
    }

    
    public static function html2text($text)
    {
        return str_replace(array(
            '</p>',
            '<p>'
        ), array(
            "\n",
            ''
        ), $text);
    }

    public static function workCount($content)
    {
        $content = preg_replace('/<[^>]+>/', '^', $content);
                preg_match_all('/[a-z0-9]+/i', $content, $mat);
        $englishCount = count($mat[0]);
                $content = str_replace('^', '', $content);
        $content = preg_replace('/[^\x{4e00}-\x{9fa5}]+/u', '', $content);
        $chineseCount = mb_strlen($content, 'utf-8');
        return $englishCount + $chineseCount;
    }
}

