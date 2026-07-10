<?php

namespace BeyondSEODeps\Illuminate\Events;

use Closure;

if (! function_exists('BeyondSEODeps\Illuminate\Events\queueable')) {
    /**
     * Create a new queued Closure event listener.
     *
     * @param  \Closure  $closure
     * @return \BeyondSEODeps\Illuminate\Events\QueuedClosure
     */
    function queueable(Closure $closure)
    {
        return new QueuedClosure($closure);
    }
}
