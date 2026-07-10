<?php

namespace BeyondSEODeps\Illuminate\Support\Facades;

use BeyondSEODeps\Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

/**
 * @method static \Illuminate\Foundation\Bus\PendingDispatch queue(string $command, array $parameters = [])
 * @method static \Illuminate\Foundation\Console\ClosureCommand command(string $command, callable $callback)
 * @method static array all()
 * @method static int call(string $command, array $parameters = [], \BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface|null $outputBuffer = null)
 * @method static int handle(\BeyondSEODeps\Symfony\Component\Console\Input\InputInterface $input, \BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface|null $output = null)
 * @method static string output()
 * @method static void terminate(\BeyondSEODeps\Symfony\Component\Console\Input\InputInterface $input, int $status)
 *
 * @see \BeyondSEODeps\Illuminate\Contracts\Console\Kernel
 */
class Artisan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleKernelContract::class;
    }
}
