<?php

return [
    'driver' => 'openswoole',

    'drivers' => [
        'openswoole' => [
            'class' => \Merjn\FastSeed\Seeder\Drivers\OpenSwoole\OpenSwooleSeeder::class,
            'workers' => env('FASTSEED_SWOOLE_WORKERS',  4),
        ]
    ]
];
