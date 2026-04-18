<?php

declare(strict_types=1);

return [
    'register_gallery_page' => true,
    'register_media_sources_resource' => true,
    'page' => [
        'accepted_file_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'video/mp4',
        ],
        'max_upload_size' => 51200,
    ],
    'picker' => [
        'accepted_file_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ],
        'max_upload_size' => 10240,
    ],
];
