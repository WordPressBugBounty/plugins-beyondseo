<?php

namespace BeyondSEODeps\Illuminate\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \BeyondSEODeps\Illuminate\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
