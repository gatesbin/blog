<?php

namespace TechSoft\Laravel\Mail;

use Illuminate\Support\Facades\Mail;
use TechSoft\Laravel\Config\ConfigUtil;

class MailUtil
{

    public static function send($email, $subject, $template, $templateData = [], $emailUserName = null, $option = [])
    {
        if (!ConfigUtil::get('systemEmailEnable')) {
            return;
        }

        static $inited = false;
        if (!$inited) {
            $inited = true;
            config([
                'mail' => [
                    'driver' => 'smtp',
                    'host' => ConfigUtil::get('systemEmailSmtpServer'),
                    'port' => ConfigUtil::get('systemEmailSmtpSsl', false) ? 465 : 25,
                    'encryption' => ConfigUtil::get('systemEmailSmtpSsl', false) ? 'ssl' : 'tls',
                    'from' => array('address' => ConfigUtil::get('systemEmailSmtpUser'), 'name' => ConfigUtil::get('systemEmailFromName', ConfigUtil::get('siteName') . ' @ ' . ConfigUtil::get('siteDomain'))),
                    'username' => ConfigUtil::get('systemEmailSmtpUser'),
                    'password' => ConfigUtil::get('systemEmailSmtpPassword'),
                ]
            ]);
        }

        $view = 'theme.' . ConfigUtil::get('siteTemplate', 'default') . '.mail.' . $template;
        if (!view()->exists($view)) {
            $view = 'theme.default.mail.' . $template;
            if (!view()->exists($view)) {
                $view = 'soft::mail.' . $template;
            }
        }

        if (!view()->exists($view)) {
            throw new \Exception('mail view not found : ' . $view);
        }

        if (null === $emailUserName) {
            $emailUserName = $email;
        }

        Mail::send($view, $templateData, function ($message) use ($email, $emailUserName, $subject, $option) {
            $message->to($email, $emailUserName)->subject($subject);
            if (!empty($option['attachment'])) {
                foreach ($option['attachment'] as $filename => $path) {
                    $message->attach($path, ['as' => $filename]);
                }
            }
        });
    }
}
