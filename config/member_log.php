<?php

return [
    'modules' => [
        'member_profile' => '會員個人資料',
        'member_auth' => '會員認證',
    ],
    'actions' => [
        'create' => '新增',
        'update' => '編輯',
        'delete' => '刪除',
    ],
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'secret',
    ],
    'retention_days' => 180,
];

