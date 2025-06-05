<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // CSRF Cookie のパスを許可

    'allowed_methods' => ['*'], // すべての HTTP メソッドを許可

    'allowed_origins' => ['http://localhost:5173'], // Vue.js のフロントエンドのURLのみ許可

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // すべてのリクエストヘッダーを許可

    'exposed_headers' => ['*'], // 必要に応じてヘッダーを公開

    'max_age' => 0,

    'supports_credentials' => true, // 認証情報を含むリクエストを許可

];
