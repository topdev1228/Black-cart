<?php
declare(strict_types=1);

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'shopify' => [
        'client_id' => env('SHOPIFY_CLIENT_ID'),
        'client_secret' => env('SHOPIFY_CLIENT_SECRET'),
        'redirect' => env('SHOPIFY_REDIRECT_URI'),
        'admin_url_path' => env('SHOPIFY_ADMIN_URL_PATH', '/admin/apps/blackcart-tbyb'),
        'admin_graphql_api_version' => env('SHOPIFY_ADMIN_GRAPHQL_API_VERSION', '2024-01'),
        'pubsub' => [
            'project' => env('PUBSUB_PROJECT_ID', env('PROJECT_ID', env('GOOGLE_CLOUD_PROJECT'))),
        ],
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
    ],

];
