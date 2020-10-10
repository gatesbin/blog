<?php

namespace TechOnline\Utils;


class ScheduleUtil
{
    
                                                                    public static function retry($param,
                                 $currentExecutionTimes,
                                 $retrySecondSequences,
                                 $retryCallback,
                                 $failCallback)
    {
        if ($currentExecutionTimes < 1) {
                    }
        if (empty($retrySequences)) {
                    }
        if ($currentExecutionTimes - 1 >= count($retrySecondSequences)) {
            $failCallback($param, $currentExecutionTimes);
            return;
        }
        $delaySeconds = $retrySecondSequences[$currentExecutionTimes - 1];
        $retryCallback($param, $currentExecutionTimes, $delaySeconds);
    }
}