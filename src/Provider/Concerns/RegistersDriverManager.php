<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Provider\Concerns;

use Merjn\FastSeed\Seeder\Drivers\DriverManager;

trait RegistersDriverManager
{
    /**
     * Register the driver manager.
     *
     * @return DriverManager
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerDriverManager(callable $callback): void
    {
        $this->app->singleton(DriverManager::class, function () use ($callback): DriverManager {
            return tap(new DriverManager(), fn ($manager) => $callback($manager));
        });
    }
}