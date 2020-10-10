<?php
namespace TechSoft\Laravel\Wechat\Message;


class MessageImage extends MessageBase
{
    public $type = MessageType::IMAGE;

    public $url;

    public function toArray()
    {
        return [
            'type' => $this->type,
            'url' => $this->url,
        ];
    }
}