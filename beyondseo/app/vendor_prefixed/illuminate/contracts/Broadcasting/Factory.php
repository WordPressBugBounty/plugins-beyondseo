<?php

namespace BeyondSEODeps\Illuminate\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \BeyondSEODeps\Illuminate\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}
