<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Seeder;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Seeder;
use Illuminate\Database\Seeder as LaravelSeeder;
use Illuminate\Support\Arr;
use Merjn\FastSeed\Actions\BenchmarkSeedingProcess;
use Merjn\FastSeed\Contracts\Seeder\Drivers\SeederInterface;
use Merjn\FastSeed\Seeder\Drivers\DriverManager;
use Merjn\FastSeed\Seeder\Exceptions\DriverNotConfiguredException;

class FastSeed extends LaravelSeeder
{
    /**
     * @var array<string> $middleware
     */
    protected array $middleware = [
        BenchmarkSeedingProcess::class,
    ];

    public function __construct(
        private readonly DriverManager $driverManager,
        private readonly Repository $config
    ) {}

    /**
     * Run the seeder in parallel.
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

        $this->applyMiddleware($driver)->run(array_map(function (string|array $seeder): Seeder|array {
            return is_string($seeder)
                ? $this->resolve($seeder)
                : $this->resolveGroup($seeder);
        }, $seeders));
    }

    /**
     * Run the seeders in parallel under the given condition.
     *
     * @param callable $callback
     * @param array<string|array> $seeders
     * @return void
     * @throws DriverNotConfiguredException
     */
    public function callParallelIf(callable $callback, array $seeders): void
    {
        $callback()
            ? $this->callParallel($seeders)
            : $this->call(Arr::flatten($seeders));
    }

    /**
     * Resolve a seeder group.
     *
     * @param array<string> $seederGroup
     * @return array<Seeder>
     */
    private function resolveGroup(array $seederGroup): array
    {
        return array_map(fn (string $seeder): Seeder => $this->resolve($seeder), $seederGroup);
    }

    /**
     * Apply middleware to the seeder.
     *
     * @param SeederInterface $seeder
     * @return SeederInterface
     */
    private function applyMiddleware(SeederInterface $seeder): SeederInterface
    {
        foreach ($this->middleware as $middleware) {
            $seeder = $this->driverManager->extend(
                $this->config->get('fastseed.driver'),
                $this->resolve($middleware)
            );
        }

        return $seeder;
    }
}