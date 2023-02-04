<?php

declare(strict_types=1);

namespace Merjn\FastSeed;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Merjn\FastSeed\Actions\BenchmarkSeeders;
use Merjn\FastSeed\Contracts\Seeder\Drivers\SeederInterface;
use Merjn\FastSeed\Seeder\Drivers\DriverManager;
use Merjn\FastSeed\Seeder\Drivers\OpenSwoole\SwooleSeederConfig;
use Monolog\Handler\StreamHandler;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Console\Logger\ConsoleLogger;

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

        $this->app->singleton(SwooleSeederConfig::class, function () {
            return new SwooleSeederConfig(
                $this->app->make('config')->get('fastseed.drivers.openswoole.workers')
            );
        });

        $this->app->singleton(DriverManager::class, function (): DriverManager {
            return tap(new DriverManager(), function ($manager) {
                $this->registerDrivers($manager);
            });
        });
    }

    protected function registerBenchmarkingIfEnabled(string $seeder): BenchmarkSeeders
    {
        return new BenchmarkSeeders(
            $this->app->make($seeder),
            new \Monolog\Logger('benchmark', [new StreamHandler('php://stdout')])
        );
    }

    protected function registerDrivers(DriverManager $manager): void
    {
        foreach (config('fastseed.drivers') as $name => $driver) {
            $instance = $this->app->make($driver['class']);
            $instance = $this->registerBenchmarkingIfEnabled($driver['class']);

            $manager->register($name, $instance);
        }
    }
}