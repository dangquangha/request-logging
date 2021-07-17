<?php
return [
    'search_daily'      => [
        'driver' => 'daily',
        'path'   => storage_path('request_logs/search_daily.log'),
        'level'  => 'emergency',
        'days'   => 7,
    ],
    'robot_counter' => [
        'driver' => 'daily',
        'level'  => 'emergency',
        'path'   => storage_path('request_logs/robots.log'),
        'days'   => 7,
    ],
    'refer' =>  [
        'driver' => 'daily',
        'level'  => 'emergency',
        'path'   => storage_path('request_logs/refer.log'),
        'days'   => 7,
    ]
];
