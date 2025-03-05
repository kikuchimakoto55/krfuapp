<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

<<<<<<< HEAD
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

=======
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // CSRF Cookie のパスを許可

    'allowed_methods' => ['*'], // すべての HTTP メソッドを許可

    'allowed_origins' => ['http://localhost:5173'], // Vue.js のフロントエンドのURLのみ許可

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // すべてのリクエストヘッダーを許可

    'exposed_headers' => ['*'], // 必要に応じてヘッダーを公開

    'max_age' => 0,

    'supports_credentials' => true, // 認証情報を含むリクエストを許可
>>>>>>> b7b2b51 (Initial commit: Laravel project setup)
];
