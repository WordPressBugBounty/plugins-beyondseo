<?php

namespace BeyondSEODeps\Doctrine\DBAL\Exception;

use BeyondSEODeps\Doctrine\DBAL\Exception;

/**
 * Exception to be thrown when invalid arguments are passed to any DBAL API
 */
class InvalidArgumentException extends Exception
{
    /** @return self */
    public static function fromEmptyCriteria()
    {
        return new self('Empty criteria was used, expected non-empty criteria');
    }
}
