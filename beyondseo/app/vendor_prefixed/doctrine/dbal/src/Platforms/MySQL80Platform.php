<?php

namespace BeyondSEODeps\Doctrine\DBAL\Platforms;

use BeyondSEODeps\Doctrine\DBAL\SQL\Builder\SelectSQLBuilder;
use BeyondSEODeps\Doctrine\Deprecations\Deprecation;
/**
 * Provides the behavior, features and SQL dialect of the MySQL 8.0 database platform.
 */
class MySQL80Platform extends MySQL57Platform
{
    /**
     * {@inheritDoc}
     *
     * @deprecated Implement {@see createReservedKeywordsList()} instead.
     */
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside('doctrine/dbal', 'https://github.com/doctrine/dbal/issues/4510', 'MySQL80Platform::getReservedKeywordsClass() is deprecated,' . ' use MySQL80Platform::createReservedKeywordsList() instead.');
        return \BeyondSEODeps\Doctrine\DBAL\Platforms\Keywords\MySQL80Keywords::class;
    }
    public function createSelectSQLBuilder(): SelectSQLBuilder
    {
        return AbstractPlatform::createSelectSQLBuilder();
    }
}