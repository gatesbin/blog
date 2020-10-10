<?php

namespace TechSoft\Laravel\Recycle;


use TechOnline\Laravel\Dao\ModelUtil;

class RecycleUtil
{
    public static function tableAdd($table, $tableId, $data)
    {
        ModelUtil::insert('recycle_table', [
            'table' => $table,
            'tableId' => $tableId,
            'content' => json_encode($data),
        ]);
    }
}