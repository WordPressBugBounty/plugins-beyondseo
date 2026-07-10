<?php

declare (strict_types=1);
namespace BeyondSEODeps\Doctrine\DBAL\Driver\API\IBMDB2;

use BeyondSEODeps\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use BeyondSEODeps\Doctrine\DBAL\Driver\Exception;
use BeyondSEODeps\Doctrine\DBAL\Exception\ConnectionException;
use BeyondSEODeps\Doctrine\DBAL\Exception\DriverException;
use BeyondSEODeps\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use BeyondSEODeps\Doctrine\DBAL\Exception\InvalidFieldNameException;
use BeyondSEODeps\Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use BeyondSEODeps\Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use BeyondSEODeps\Doctrine\DBAL\Exception\SyntaxErrorException;
use BeyondSEODeps\Doctrine\DBAL\Exception\TableExistsException;
use BeyondSEODeps\Doctrine\DBAL\Exception\TableNotFoundException;
use BeyondSEODeps\Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use BeyondSEODeps\Doctrine\DBAL\Query;
/**
 * @internal
 *
 * @link \https://www.ibm.com/docs/en/db2/11.5?topic=messages-sql
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        switch ($exception->getCode()) {
            case -104:
                return new SyntaxErrorException($exception, $query);
            case -203:
                return new NonUniqueFieldNameException($exception, $query);
            case -204:
                return new TableNotFoundException($exception, $query);
            case -206:
                return new InvalidFieldNameException($exception, $query);
            case -407:
                return new NotNullConstraintViolationException($exception, $query);
            case -530:
            case -531:
            case -532:
            case -20356:
                return new ForeignKeyConstraintViolationException($exception, $query);
            case -601:
                return new TableExistsException($exception, $query);
            case -803:
                return new UniqueConstraintViolationException($exception, $query);
            case -1336:
            case -30082:
                return new ConnectionException($exception, $query);
        }
        return new DriverException($exception, $query);
    }
}