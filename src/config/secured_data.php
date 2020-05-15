<?php

return [
    
    'attachment' => [
        'max_size' => env('SECURED_DATA_ATTACHMENT_MAX_SIZE', '102400'),
        'mimetypes' => [
            'image/*',
            'text/*',

            'application/pdf',

            /** Zip & Rar */
            'application/x-rar-compressed', 
            'application/octet-stream',
            'application/zip', 
            'application/octet-stream', 
            'application/x-zip-compressed', 
            'application/vnd.rar',
            'application/x-rar',
            'multipart/x-zip',

            /** MS Office */
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-access'
        ]
    ],

    'plain_data' => [
        'max_size' => env('SECURED_DATA_PLAIN_MAX_SIZE', '10000')
    ]

];