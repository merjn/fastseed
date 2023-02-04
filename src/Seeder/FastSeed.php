<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Seeder;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Seeder as LaravelSeeder;
use Merjn\FastSeed\Seeder\Drivers\DriverManager;
use Merjn\FastSeed\Seeder\Exceptions\DriverNotConfiguredException;

class FastSeed extends LaravelSeeder
{
    public function __construct(
        private readonly DriverManager $driverManager,
        private readonly Repository $config
    ) {}

    /**
     * Run the seeder in parallel.
     *
     * The order of the seeders is not guaranteed.
     *
     * @param array $seeders
     * @return void
     * @throws DriverNotConfiguredException
     */
    public function callParallel(array $seeders): void
    {
        $driver = $this->driverManager->driver($this->config->get('fastseed.driver'));
        if ($driver === null) {
            throw new DriverNotConfiguredException();
        }

        $driver->run(array_map(fn (string $seeder) => $this->resolve($seeder), $seeders));
    }

    /**
     * Run the seeders in parallel under the given condition.
     *
     * @param callable $callback
     * @param array $seeders
     * @return void
     * @throws DriverNotConfiguredException
     */
    public function callParallelIf(callable $callback, array $seeders): void
    {
        $callback()
            ? $this->callParallel($seeders)
            : $this->call($seeders);
    }
}