<?php
namespace TechSoft\Laravel\Wechat\Message;


class MessageText extends MessageBase
{
    public $type = MessageType::TEXT;

    public $content;

    public function toArray()
    {
        return [
            'type' => $this->type,
            'content' => $this->content,
        ];
    }
}