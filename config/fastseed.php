<?php

return [
    'driver' => env('openswoole', 'FASTSEED_DRIVER'),

    'drivers' => [
        'openswoole' => [
            'class' => \Merjn\FastSeed\Seeder\Drivers\OpenSwoole\OpenSwooleSeeder::class,
            'workers' => env('FASTSEED_SWOOLE_WORKERS',  4),
        ]
    ]
];
