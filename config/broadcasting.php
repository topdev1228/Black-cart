<?php
declare(strict_types=1);

return [

    'connections' => [
        'pubsub' => [
            'driver' => 'pubsub',
            'project_id' => env('PUBSUB_PROJECT_ID', env('GOOGLE_CLOUD_PROJECT')),
        ],
    ],

];
