<?php
declare(strict_types=1);

return [

    'connections' => [
        'cloudtasks' => [
            'driver' => 'cloudtasks',
            'project' => env('CLOUD_TASKS_PROJECT', env('PROJECT_ID', 'blackcart')),
            'location' => env('CLOUD_TASKS_LOCATION', ''),
            'handler' => env('CLOUD_TASKS_HANDLER', ''),
            'queue' => env('CLOUD_TASKS_QUEUE', 'default'),
            'service_account_email' => env('CLOUD_TASKS_SERVICE_EMAIL', ''),
            'signed_audience' => false, // the audience is set incorrectly due to differences in hostnames on publisher vs subscribers
        ],
    ],

];
