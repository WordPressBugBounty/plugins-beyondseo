<?php

namespace BeyondSEODeps\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \BeyondSEODeps\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
