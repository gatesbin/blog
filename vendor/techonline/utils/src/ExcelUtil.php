<?php

namespace TechOnline\Utils;


use Maatwebsite\Excel\Facades\Excel;

class ExcelUtil
{
    public static function save($pathname, $list)
    {
        $folder = FileUtil::getAndEnsurePathnameFolder($pathname);
        $basename = FileUtil::getPathnameFilename($pathname, false);
        return Excel::create($basename, function ($excel) use (&$list) {
            $excel->sheet('data', function ($sheet) use (&$list) {
                $formats = [];
                for ($i = 0; $i < count($list[0]); $i++) {
                    $formats[\PHPExcel_Cell::stringFromColumnIndex($i)] = '@';
                }
                $sheet->setColumnFormat($formats);
                $sheet->setAutoSize(true);
                $sheet->rows($list, true);
            });
        })->store('xlsx', $folder);
    }
}