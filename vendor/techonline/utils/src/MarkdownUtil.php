<?php

namespace TechOnline\Utils;

use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class MarkdownUtil
{
    public static function convertToHtml($markdown)
    {
        $converter = new CommonMarkConverter([
            'renderer' => [
                'soft_break' => "<br />",
            ],
        ]);
        return $converter->convertToHtml($markdown);
    }

}