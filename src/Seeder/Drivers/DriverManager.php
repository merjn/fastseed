<?php

namespace Merjn\FastSeed\Seeder\Drivers;

use Merjn\FastSeed\Contracts\Drivers\SeederInterface;

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