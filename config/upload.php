<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 圖片類
    |--------------------------------------------------------------------------
    */
    'image' => [
        'mimes'      => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'mime_types'  => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'max_size'    => 5120, // KB，5MB
    ],

    /*
    |--------------------------------------------------------------------------
    | 文件類
    |--------------------------------------------------------------------------
    */
    'document' => [
        'mimes'      => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
        'mime_types'  => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ],
        'max_size'    => 20480, // KB，20MB
    ],

    /*
    |--------------------------------------------------------------------------
    | 影片類
    |--------------------------------------------------------------------------
    */
    'video' => [
        'mimes'      => ['mp4', 'avi', 'mov', 'wmv'],
        'mime_types'  => ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv'],
        'max_size'    => 102400, // KB，100MB
    ],
];
