<?php

namespace TechOnline\Laravel\Exception;


class TodoException extends \Exception
{
    public static function throws()
    {
        throw new TodoException();
    }
}