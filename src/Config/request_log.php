<?php
return [
    'search_daily'      => [
        'driver' => 'daily',
        'tap'    => [Workable\RequestLogging\Logging\HistorySearchFormatter::class],
        'path'   => storage_path('request_logs/search_daily.log'),
        'level'  => 'info',
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
