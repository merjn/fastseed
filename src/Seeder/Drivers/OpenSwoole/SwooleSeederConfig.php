<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Seeder\Drivers\OpenSwoole;

class SwooleSeederConfig
{
    public const DEFAULT_WORKERS = 4;

    public function __construct(
        public readonly int $workers = self::DEFAULT_WORKERS
    ) { }
}