<?php

declare(strict_types=1);

namespace Merjn\FastSeed;

use Merjn\FastSeed\Seeder\Drivers\DriverManager;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FastSeedServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('fastseed')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile();
            });

        $this->app->singleton(DriverManager::class, function (): DriverManager {
            return tap(new DriverManager(), function ($manager) {
                $this->registerDrivers($manager);
            });
        });
    }

    protected function registerDrivers(DriverManager $manager): void
    {
        foreach (config('fastseed.drivers') as $name => $driver) {
            $manager->register($name, $this->app->make($driver['class']));
        }
    }
}