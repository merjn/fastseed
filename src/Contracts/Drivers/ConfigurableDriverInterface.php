<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Contracts\Drivers;

interface ConfigurableDriverInterface
{
    public function configure(mixed $config): void;
}