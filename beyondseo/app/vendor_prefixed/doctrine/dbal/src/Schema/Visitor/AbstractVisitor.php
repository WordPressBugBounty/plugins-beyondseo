<?php

namespace BeyondSEODeps\Doctrine\DBAL\Schema\Visitor;

use BeyondSEODeps\Doctrine\DBAL\Schema\Column;
use BeyondSEODeps\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use BeyondSEODeps\Doctrine\DBAL\Schema\Index;
use BeyondSEODeps\Doctrine\DBAL\Schema\Schema;
use BeyondSEODeps\Doctrine\DBAL\Schema\Sequence;
use BeyondSEODeps\Doctrine\DBAL\Schema\Table;

/**
 * Abstract Visitor with empty methods for easy extension.
 *
 * @deprecated
 */
class AbstractVisitor implements Visitor, NamespaceVisitor
{
    public function acceptSchema(Schema $schema)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function acceptNamespace($namespaceName)
    {
    }

    public function acceptTable(Table $table)
    {
    }

    public function acceptColumn(Table $table, Column $column)
    {
    }

    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    public function acceptIndex(Table $table, Index $index)
    {
    }

    public function acceptSequence(Sequence $sequence)
    {
    }
}
