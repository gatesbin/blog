<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

class FieldLink extends BaseField
{
    public $placeholder = '链接';

    public function viewHtml(&$data)
    {
        if ($data) {
            if ($this->placeholder) {
                return '<a href="' . $data . '" target="_blank">' . htmlspecialchars($this->placeholder) . '</a>';
            }
            return '<a href="' . $data . '" target="_blank">' . htmlspecialchars($data) . '</a>';
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            if ($this->placeholder) {
                return '<a href="' . $data . '" target="_blank">' . htmlspecialchars($this->placeholder) . '</a>';
            }
            return '<a href="' . $data . '" target="_blank">' . htmlspecialchars($data) . '</a>';
        }
        return '';
    }
}
