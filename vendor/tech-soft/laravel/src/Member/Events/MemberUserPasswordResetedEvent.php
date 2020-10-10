<?php

namespace TechSoft\Laravel\Member\Events;


class MemberUserPasswordResetedEvent
{
    public $memberUserId;
    public $newPassword;

    public function __construct($memberUserId, $newPassword)
    {
        $this->memberUserId = $memberUserId;
        $this->newPassword = $newPassword;
    }

}