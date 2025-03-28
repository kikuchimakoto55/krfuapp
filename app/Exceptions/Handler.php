<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * カスタムエラーレスポンス
     */
    public function render($request, Throwable $exception)
    {
        // 403 Forbidden の場合に詳細メッセージを返す
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            return response()->json([
                'message' => '403 Forbidden: ' . $exception->getMessage(),
                'exception' => get_class($exception),
            ], 403);
        }

        return parent::render($request, $exception);
    }
}
