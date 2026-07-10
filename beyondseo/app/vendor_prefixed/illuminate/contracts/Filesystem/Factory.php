<?php

namespace BeyondSEODeps\Illuminate\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \BeyondSEODeps\Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
