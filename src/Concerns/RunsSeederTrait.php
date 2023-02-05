<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Concerns;

trait RunsSeederTrait
{
    /**
     * Run one or more seeders.
     *
     * @param string|array $seeders
     * @return void
     */
    public function runSeeder(string|array $seeders): void
    {
        if (is_string($seeders)) {
            $seeders = [$seeders];
        }

        foreach ($seeders as $seeder) {
            if (!method_exists($seeder, 'run')) {
                throw new \InvalidArgumentException("Seeder {$seeder} does not have a run method");
            }

            $seeder->run();
        }
    }
}