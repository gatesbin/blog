<?php

namespace TechSoft\Laravel\Admin\Cms\Field ;


use Illuminate\Support\Str;

class FieldAlias extends BaseField
{
    public $length = 32;

    public function addHtml()
    {
        return null;
    }

    public function inputGet($inputAll)
    {
        if (empty($inputAll[$this->key])) {
            return strtolower(Str::random($this->length));
        }
        return $inputAll[$this->key];
    }


}
