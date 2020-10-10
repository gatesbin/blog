<?php

namespace TechSoft\Laravel\Recycle;


use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Dao\TenantModelUtil;

class TenantRecycleUtil
{
    public static function tableAdd($tanent, $table, $tableId, $data)
    {
        TenantModelUtil::insert($tanent, 'tenant_recycle_table', [
            'table' => $table,
            'tableId' => $tableId,
            'content' => json_encode($data),
        ]);
    }
}