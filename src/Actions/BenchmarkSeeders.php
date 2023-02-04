<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Actions;

use Merjn\FastSeed\Concerns\BenchmarksCallsTrait;
use Merjn\FastSeed\Contracts\Seeder\Drivers\SeederInterface;
use Psr\Log\LoggerInterface;

class BenchmarkSeeders implements SeederInterface
{
    use BenchmarksCallsTrait;

    public function __construct(
        private readonly SeederInterface $parent,
        private readonly LoggerInterface $logger
    ) { }

    public function run(array $seeders): void
    {
        $this->logger->info("Parallel testing started. This may take a while...");

        $time = $this->benchmark(function () use ($seeders) {
            $this->parent->run($seeders);
        });

        $this->logger->info("Running seeders took {$time} seconds");
    }
}