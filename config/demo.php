<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the CMS runs as a read-mostly commercial demo: seeded
    | content, a visible banner, and guards against destructive baseline changes.
    | Keep DEMO_MODE=false in production unless intentionally hosting a demo.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

    'allow_in_production' => (bool) env('DEMO_ALLOW_IN_PRODUCTION', false),

    'protected_user_emails' => [
        'demo-superadmin@contentflow.test',
        'demo-admin@contentflow.test',
        'demo-editor@contentflow.test',
    ],

];
