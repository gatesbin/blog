<?php

namespace TechSoft\Laravel\Member\Events;


class MemberUserLoginedEvent
{
    public $memberUserId;

    public function __construct($memberUserId)
    {
        $this->memberUserId = $memberUserId;
    }


}