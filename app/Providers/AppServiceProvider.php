<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // --- NoHtml カスタムバリデーション（HTMLタグ混入を禁止） ---
        $noHtml = function ($attribute, $value) {
            if ($value === null || $value === '') return true;
            // ゼロ幅除去 → strip_tags でタグが消えるならNG → 角括弧タグ検知でもNG
            $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', (string) $value);
            if ($v !== strip_tags($v)) return false;
            return !preg_match('/<[^>]*>/', $v);
        };

        // 既存の書き方に合わせ、大小2つのキーを登録しておく
        Validator::extend('NoHtml', $noHtml);
        Validator::replacer('NoHtml', fn () => ':attribute にHTMLタグは使用できません。');
        Validator::extend('no_html', $noHtml);
        Validator::replacer('no_html', fn () => ':attribute にHTMLタグは使用できません。');
    }
}
