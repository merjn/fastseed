<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Provider\Concerns;

use Merjn\FastSeed\Seeder\Drivers\OpenSwoole\OpenSwooleSeeder;
use Merjn\FastSeed\Seeder\Drivers\OpenSwoole\SwooleSeederConfig;

trait RegistersSwooleSeederTrait
{
    /**
     * Register tne OpenSwoole seeder instance.
     *
     * @return OpenSwooleSeeder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerSwooleSeeder(): OpenSwooleSeeder
    {
        return tap($this->createOpenswooleInstance(), function (OpenSwooleSeeder $seeder): void {
            $this->app->singleton(OpenSwooleSeeder::class, $seeder);
        });
    }

    /**
     * Create a new instance of the OpenSwoole seeder.
     *
     * @return OpenSwooleSeeder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createOpenswooleInstance(): OpenSwooleSeeder
    {
        return new OpenSwooleSeeder(
            new SwooleSeederConfig(
                $this->app->make('config')->get('fastseed.openswoole.workers', null)
            )
        );
    }
}