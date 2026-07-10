<?php

namespace BeyondSEODeps\Doctrine\DBAL\Schema\Visitor;

use BeyondSEODeps\Doctrine\DBAL\Schema\Column;
use BeyondSEODeps\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use BeyondSEODeps\Doctrine\DBAL\Schema\Index;
use BeyondSEODeps\Doctrine\DBAL\Schema\Schema;
use BeyondSEODeps\Doctrine\DBAL\Schema\SchemaException;
use BeyondSEODeps\Doctrine\DBAL\Schema\Sequence;
use BeyondSEODeps\Doctrine\DBAL\Schema\Table;

/**
 * Schema Visitor used for Validation or Generation purposes.
 *
 * @deprecated
 */
interface Visitor
{
    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptSchema(Schema $schema);

    /** @return void */
    public function acceptTable(Table $table);

    /** @return void */
    public function acceptColumn(Table $table, Column $column);

    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint);

    /** @return void */
    public function acceptIndex(Table $table, Index $index);

    /** @return void */
    public function acceptSequence(Sequence $sequence);
}
