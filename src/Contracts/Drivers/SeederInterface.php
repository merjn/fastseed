<?php

namespace Merjn\FastSeed\Contracts\Drivers;

interface SeederInterface
{
    public function run(array $seeders): void;
}