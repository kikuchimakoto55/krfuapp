<?php

return [
    'accepted' => ':attribute を承認してください。',
    'email' => ':attribute は有効なメールアドレス形式で指定してください。',
    'unique' => ':attribute は既に登録されています。',
    'required' => ':attribute は必須項目です。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'min' => [
        'string' => ':attribute は最低 :min 文字以上で入力してください。',
    ],

    'custom' => [
        'email' => [
            'unique' => 'このメールアドレスは既に登録済みです。',
        ],
    ],

    'attributes' => [
        'username_sei' => '姓',
        'username_mei' => '名',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'birthday' => '生年月日',
        'sex' => '性別',
    ],
];
