<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    |
    | Disk, upload limits, and MIME allowlists for the admin media library.
    | Run `php artisan storage:link` so public disk files are web-accessible.
    |
    */

    'disk' => env('MEDIA_DISK', 'public'),

    'max_upload_kilobytes' => (int) env('MEDIA_MAX_UPLOAD_KB', 5120),

    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ],

    'allowed_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
    ],

];
