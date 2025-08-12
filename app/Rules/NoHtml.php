<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoHtml implements Rule
{
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return true;
        }
        // 任意のHTMLタグを含んでいたらNG（<...> を検出）
        return !preg_match('/<[^>]+>/', $value);
    }

    public function message()
    {
        return '無効な文字（HTMLタグ）は使用できません。';
    }
}