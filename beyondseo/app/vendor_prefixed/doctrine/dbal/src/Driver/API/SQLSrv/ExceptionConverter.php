<?php

declare (strict_types=1);
namespace BeyondSEODeps\Doctrine\DBAL\Driver\API\SQLSrv;

use BeyondSEODeps\Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use BeyondSEODeps\Doctrine\DBAL\Driver\Exception;
use BeyondSEODeps\Doctrine\DBAL\Exception\ConnectionException;
use BeyondSEODeps\Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
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
 * @link \https://docs.microsoft.com/en-us/sql/relational-databases/errors-events/database-engine-events-and-errors
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        switch ($exception->getCode()) {
            case 102:
                return new SyntaxErrorException($exception, $query);
            case 207:
                return new InvalidFieldNameException($exception, $query);
            case 208:
                return new TableNotFoundException($exception, $query);
            case 209:
                return new NonUniqueFieldNameException($exception, $query);
            case 515:
                return new NotNullConstraintViolationException($exception, $query);
            case 547:
            case 4712:
                return new ForeignKeyConstraintViolationException($exception, $query);
            case 2601:
            case 2627:
                return new UniqueConstraintViolationException($exception, $query);
            case 2714:
                return new TableExistsException($exception, $query);
            case 3701:
            case 15151:
                return new DatabaseObjectNotFoundException($exception, $query);
            case 11001:
            case 18456:
                return new ConnectionException($exception, $query);
        }
        return new DriverException($exception, $query);
    }
}