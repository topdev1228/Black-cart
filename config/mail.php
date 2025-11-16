<?php
declare(strict_types=1);

return [

    'mailers' => [
        'sendgrid' => [
            'transport' => 'sendgrid',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@blackcart.com'),
        'name' => env('MAIL_FROM_NAME', 'Blackcart'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */
    'markdown' => [
        'theme' => 'blackcart',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
