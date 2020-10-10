<?php

namespace TechSoft\Laravel\Forum\Events;

class ThreadHasNewPost
{
    public $thread;
    public $post;
    public $postPage;
}