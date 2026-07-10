<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Event;

use BeyondSEODeps\Doctrine\Common\EventArgs;
use BeyondSEODeps\Doctrine\DBAL\Schema\Schema;
use BeyondSEODeps\Doctrine\DBAL\Schema\Table;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Event Args used for the Events::postGenerateSchemaTable event.
 *
 * @link        www.doctrine-project.com
 */
class GenerateSchemaTableEventArgs extends EventArgs
{
    /** @var ClassMetadata */
    private $classMetadata;

    /** @var Schema */
    private $schema;

    /** @var Table */
    private $classTable;

    public function __construct(ClassMetadata $classMetadata, Schema $schema, Table $classTable)
    {
        $this->classMetadata = $classMetadata;
        $this->schema        = $schema;
        $this->classTable    = $classTable;
    }

    /** @return ClassMetadata */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /** @return Schema */
    public function getSchema()
    {
        return $this->schema;
    }

    /** @return Table */
    public function getClassTable()
    {
        return $this->classTable;
    }
}
