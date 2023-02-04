<?php

namespace Merjn\FastSeed\Contracts\Seeder\Drivers;

interface SeederInterface
{
    public function run(array $seeders): void;
}