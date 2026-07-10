<?php

namespace BeyondSEODeps\Cron;

interface FieldFactoryInterface
{
    public function getField(int $position): FieldInterface;
}
