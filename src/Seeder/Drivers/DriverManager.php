<?php

namespace Merjn\FastSeed\Seeder\Drivers;

use Illuminate\Database\Seeder;
use Merjn\FastSeed\Contracts\Seeder\Drivers\SeederInterface;

class DriverManager
{
    private array $drivers = [];

    /**
     * Register a new driver.
     *
     * @param string $driverName
     * @param SeederInterface $driver
     * @return void
     */
    public function register(string $driverName, SeederInterface $driver)
    {
        $this->drivers[$driverName] = $driver;
    }

    /**
     * Extend an existing driver with a decorator.
     *
     * @param string $driverName
     * @param callable $callback
     * @return SeederInterface
     */
    public function extend(string $driverName, callable $callback): SeederInterface
    {
        return $this->drivers[$driverName] = $callback($this->drivers[$driverName]);
    }

    /**
     * Get a driver instance.
     *
     * @param string $driver
     * @return SeederInterface|null
     */
    public function driver(string $driver = ''): ?SeederInterface
    {
        return $this->drivers[$driver] ?? null;
    }
}