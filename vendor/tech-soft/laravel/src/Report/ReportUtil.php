<?php

namespace TechSoft\Laravel\Report;


use TechOnline\Laravel\Dao\ModelUtil;

class ReportUtil
{
    public static function countDaily($tableName, $tableWhere, $fromDay, $toDay)
    {
        $startTimestamp = strtotime($fromDay);
        $toTimestamp = strtotime($toDay);
        $reports = [];
        for ($timestamp = $startTimestamp; $timestamp <= $toTimestamp; $timestamp += 24 * 3600) {
            $reports[date('Y-m-d', $timestamp)] = null;
        }
        $counts = ModelUtil::model('report_count_daily')
            ->where(['tableName' => $tableName, 'tableWhere' => json_encode($tableWhere)])
            ->where('day', '>=', $fromDay)
            ->where('day', '<=', $toDay)
            ->get();
        foreach ($counts as $count) {
            $reports[date('Y-m-d', strtotime($count->day))] = $count->cnt;
        }
        foreach ($reports as $reportDay => $reportCount) {
            if (null === $reportCount) {
                $reports[$reportDay] = self::countDayFromTable($tableName, $tableWhere, $reportDay);
            }
        }
        return [
            'time' => array_keys($reports),
            'value' => array_values($reports),
        ];
    }

    private static function countDayFromTable($tableName, $tableWhere, $day)
    {
        $count = ModelUtil::model($tableName)
            ->where($tableWhere)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($day)))
            ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($day)))
            ->count();
        if (strtotime($day) < strtotime(date('Y-m-d', time()))) {
            ModelUtil::insert('report_count_daily', [
                'tableName' => $tableName,
                'tableWhere' => json_encode($tableWhere),
                'day' => $day,
                'cnt' => $count,
            ]);
        }
        return $count;
    }
}