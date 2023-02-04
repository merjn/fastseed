<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Concerns;

trait BenchmarksCallsTrait
{
    /**
     * Run a benchmark before and after the call.
     *
     * @param callable $callback
     * @return float
     */
    private function benchmark(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        return microtime(true) - $start;
    }
}