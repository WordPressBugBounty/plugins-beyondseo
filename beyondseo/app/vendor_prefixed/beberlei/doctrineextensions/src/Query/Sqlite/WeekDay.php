<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Sqlite;

/** @author Tarjei Huse <tarjei.huse@gmail.com> */
class WeekDay extends NumberFromStrfTime
{
    protected function getFormat(): string
    {
        return '%w';
    }
}
