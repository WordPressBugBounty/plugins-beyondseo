<?php

namespace BeyondSEODeps\Illuminate\Support\Traits;

trait Tappable
{
    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param  callable|null  $callback
     * @return $this|\BeyondSEODeps\Illuminate\Support\HigherOrderTapProxy
     */
    public function tap($callback = null)
    {
        return beondseodeps_tap($this, $callback);
    }
}
