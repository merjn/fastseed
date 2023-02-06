<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Provider;

use Merjn\FastSeed\Seeder\Drivers\DriverManager;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FastSeedServiceProvider extends PackageServiceProvider
{
    use Concerns\RegistersDriverManager;
    use Concerns\RegistersSwooleSeederTrait;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('fastseed')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile();
            });

        $this->registerDriverManager(function (DriverManager $manager): void {
            $manager->register('openswoole', $this->registerSwooleSeeder());
        });
    }
}