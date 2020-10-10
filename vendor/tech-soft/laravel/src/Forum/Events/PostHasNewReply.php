<?php

namespace TechSoft\Laravel\Forum\Events;

class PostHasNewReply
{
    public $thread;
    public $yourPost;
    public $replyPost;
    public $replyPostPage;
}