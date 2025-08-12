<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /** @var array<string> HTMLを許可するキー（グローバル除外） */
    protected array $except = [
        // 例: 'rich_text', 'description'
    ];

    /** @var array<string,int> 文字数上限（どうしてもサーバ側で切りたい場合のみ使用） */
    protected array $maxLengths = [
        // 'remarks' => 1000,
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (!is_string($value)) return;

            // 1) 前後空白・Unicode空白・ゼロ幅の正規化
            $value = $this->normalizeWhitespace($value);

            // 2) except に含まれるキーは HTML を保持（安全のため空白正規化のみ）
            if (in_array($key, $this->except, true)) {
                return;
            }

            // 3) 危険タグ（script/style/iframe/object/embed）は中身ごと除去
            $value = preg_replace(
                '/<\s*(script|style|iframe|object|embed)[^>]*>.*?<\s*\/\s*\1\s*>/is',
                '',
                $value
            );

            // 4) 残りのタグは一律禁止（最低限仕様）
            $value = strip_tags($value);

            // 5) 制御文字（C0/C1とDEL）除去
            $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        });

        // 6) 文字数ガード（任意。通常はFormRequestの max で弾く運用推奨）
        foreach ($this->maxLengths as $k => $len) {
            if (isset($input[$k]) && is_string($input[$k])) {
                // 文字数ベースで守りたい場合は mb_substr を推奨
                if (mb_strlen($input[$k], 'UTF-8') > $len) {
                    $input[$k] = mb_substr($input[$k], 0, $len, 'UTF-8');
                }
            }
        }

        $request->merge($input);
        return $next($request);
    }

    private function normalizeWhitespace(string $v): string
    {
        // NBSP と 全角スペースを半角へ
        $v = str_replace(["\u{00A0}", "\u{3000}"], ' ', $v);
        // 連続空白を1つへ（改行は保持したいなら \h に変更）
        $v = preg_replace('/\s+/u', ' ', $v);
        // ゼロ幅類の除去
        $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v);
        // 前後トリム
        return trim($v);
    }
}