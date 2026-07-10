<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;

/**
 * Property that contains the initializer for a lazy object
 */
class InitializationTracker extends PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(IdentifierSuffixer::getIdentifier('initializationTracker'));

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var bool tracks initialization status - true while the object is initializing');
        $this->setDefaultValue(false);
    }
}
