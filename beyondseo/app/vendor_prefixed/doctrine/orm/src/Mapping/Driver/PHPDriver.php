<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping\Driver;

use BeyondSEODeps\Doctrine\Deprecations\Deprecation;
use BeyondSEODeps\Doctrine\Persistence\Mapping\Driver\FileLocator;
use BeyondSEODeps\Doctrine\Persistence\Mapping\Driver\PHPDriver as CommonPHPDriver;

/**
 * {@inheritDoc}
 *
 * @deprecated this driver will be removed, use StaticPHPDriver or other mapping drivers instead.
 */
class PHPDriver extends CommonPHPDriver
{
    /** @param string|string[]|FileLocator $locator */
    public function __construct($locator)
    {
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/issues/9277',
            'PHPDriver is deprecated, use StaticPHPDriver or other mapping drivers instead.'
        );

        parent::__construct($locator);
    }
}
