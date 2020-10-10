<?php

namespace TechSoft\Laravel\Mail;

use TechOnline\Laravel\Job\BaseJob;
use TechOnline\Laravel\Log\Logger;

class MailSendJob extends BaseJob
{
    public $email;
    public $subject;
    public $template;
    public $templateData = [];
    public $emailUserName = null;
    public $option = [];

    public static function create($email, $subject, $template, $templateData = [], $emailUserName = null, $option = [], $delay = 0)
    {
        $job = new MailSendJob();
        $job->email = $email;
        $job->subject = $subject;
        $job->template = $template;
        $job->templateData = $templateData;
        $job->emailUserName = $emailUserName;
        $job->option = $option;
        $job->onQueue('DefaultJob');
        if ($delay > 0) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        Logger::info('Email', 'Start', $this->email . ' -> ' . $this->subject . ' -> ' . $this->template);
        MailUtil::send($this->email, $this->subject, $this->template, $this->templateData, $this->emailUserName, $this->option);
        Logger::info('Email', 'End', $this->email . ' -> ' . $this->subject);
    }
}
