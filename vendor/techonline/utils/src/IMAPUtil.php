<?php

namespace TechOnline\Utils;

class IMAPUtil
{
    
    public static function create($imapPath, $username, $password, $serverEncoding = 'UTF-8', $attachmentsDir = '/tmp')
    {
        $mailbox = new \PhpImap\Mailbox($imapPath, $username, $password, $attachmentsDir, $serverEncoding);
        $mailbox->setAttachmentsIgnore(true);
        return $mailbox;
    }

    
    public static function noAttachments($mailbox)
    {
        $mailbox->setAttachmentsIgnore(true);
    }

    
    public static function mailIds($mailbox)
    {
        return $mailbox->searchMailbox();
    }

    
    public static function get($mailbox, $mailId)
    {
        return $mailbox->getMail($mailId);
    }
}
