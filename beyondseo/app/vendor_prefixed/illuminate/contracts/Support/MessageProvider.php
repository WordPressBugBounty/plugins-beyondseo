<?php

namespace BeyondSEODeps\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \BeyondSEODeps\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
