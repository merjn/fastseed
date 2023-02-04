<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Seeder\Exceptions;

class DriverNotConfiguredException extends \Exception
{
    public function __construct()
    {
        parent::__construct("No driver configured for fastseed");
    }
}