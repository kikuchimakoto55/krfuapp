<?php

return [
    'accepted' => ':attribute を承認してください。',
    'email' => ':attribute は有効なメールアドレス形式で指定してください。',
    'unique' => ':attribute は既に登録されています。',
    'required' => ':attribute は必須項目です。',
    'integer' => ':attribute は整数で指定してください。',
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
        // 会員関連
        'username_sei' => '姓',
        'username_mei' => '名',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'birthday' => '生年月日',
        'sex' => '性別',

        // チーム関連
        'team_id' => 'チームID',
        'year' => '年度',
        'team_name' => 'チーム名',
        'representative_name' => '代表者氏名',
        'representative_email' => '代表者メール',
        'male_members' => '男子人数',
        'female_members' => '女子人数',
    ],
];