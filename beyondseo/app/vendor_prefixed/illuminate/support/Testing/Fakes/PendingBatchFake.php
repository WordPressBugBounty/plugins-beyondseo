<?php

namespace BeyondSEODeps\Illuminate\Support\Testing\Fakes;

use BeyondSEODeps\Illuminate\Bus\PendingBatch;
use BeyondSEODeps\Illuminate\Support\Collection;

class PendingBatchFake extends PendingBatch
{
    /**
     * The fake bus instance.
     *
     * @var \BeyondSEODeps\Illuminate\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending batch instance.
     *
     * @param  \BeyondSEODeps\Illuminate\Support\Testing\Fakes\BusFake  $bus
     * @param  \BeyondSEODeps\Illuminate\Support\Collection  $jobs
     * @return void
     */
    public function __construct(BusFake $bus, Collection $jobs)
    {
        $this->bus = $bus;
        $this->jobs = $jobs;
    }

    /**
     * Dispatch the batch.
     *
     * @return \BeyondSEODeps\Illuminate\Bus\Batch
     */
    public function dispatch()
    {
        return $this->bus->recordPendingBatch($this);
    }
}
