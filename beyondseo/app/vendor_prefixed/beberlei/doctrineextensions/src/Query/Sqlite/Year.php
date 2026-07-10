<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Sqlite;

/** @author Tarjei Huse <tarjei.huse@gmail.com> */
class Year extends NumberFromStrfTime
{
    protected function getFormat(): string
    {
        return '%Y';
    }
}
