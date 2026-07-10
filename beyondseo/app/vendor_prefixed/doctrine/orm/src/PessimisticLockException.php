<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM;

use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;

class PessimisticLockException extends ORMException
{
    /** @return PessimisticLockException */
    public static function lockFailed()
    {
        return new self('The pessimistic lock failed.');
    }
}
