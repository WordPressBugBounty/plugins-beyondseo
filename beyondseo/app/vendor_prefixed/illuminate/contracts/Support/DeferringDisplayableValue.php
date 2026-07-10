<?php

namespace BeyondSEODeps\Illuminate\Contracts\Support;

interface DeferringDisplayableValue
{
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return \BeyondSEODeps\Illuminate\Contracts\Support\Htmlable|string
     */
    public function resolveDisplayableValue();
}
