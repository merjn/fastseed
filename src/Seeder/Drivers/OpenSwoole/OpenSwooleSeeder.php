<?php

declare(strict_types=1);

namespace Merjn\FastSeed\Seeder\Drivers\OpenSwoole;

use Illuminate\Support\Collection;
use Merjn\FastSeed\Contracts\Seeder\Drivers\SeederInterface;
use OpenSwoole\Coroutine\Channel;
use OpenSwoole\Event;
use OpenSwoole\Runtime;

class OpenSwooleSeeder implements SeederInterface
{
    public function __construct(
        private readonly SwooleSeederConfig $config
    ) { }

    /**
     * Run each database seeder in parallel.
     *
     * @param array $seeders
     * @return void
     */
    public function run(array $seeders): void
    {
        $channel = new Channel();

        Runtime::enableCoroutine();

        $this->bootWorkers($channel);
        $this->pushSeeders($channel, $seeders);

        Event::wait();
    }

    /**
     * Boot the workers.
     *
     * @param Channel $channel
     * @return Collection
     */
    private function bootWorkers(Channel $channel): Collection
    {
        return collect(range(1, $this->config->workers))->map(function () use ($channel) {
            return go(function () use ($channel) {
                while (true) {
                    $seeder = $channel->pop();
                    if ($seeder === false) {
                        break;
                    }

                    $seeder->run();
                }
            });
        });
    }

    /**
     * Create a coroutine that pushes the seeders to the channel.
     *
     * @param Channel $channel
     * @param array $seeders
     * @return void
     */
    private function pushSeeders(Channel $channel, array $seeders): void
    {
        go(function() use ($channel, $seeders) {
            foreach ($seeders as $seeder) {
                $channel->push($seeder);
            }

            $channel->close();
        });
    }
}