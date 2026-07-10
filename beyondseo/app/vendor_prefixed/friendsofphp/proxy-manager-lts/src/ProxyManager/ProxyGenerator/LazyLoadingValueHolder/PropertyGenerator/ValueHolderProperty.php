<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\DocBlockGenerator;
use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;
use ReflectionClass;

/**
 * Property that contains the wrapped value of a lazy loading proxy
 */
class ValueHolderProperty extends PropertyGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ReflectionClass $type)
    {
        parent::__construct(IdentifierSuffixer::getIdentifier('valueHolder'));

        $docBlock = new DocBlockGenerator();

        $docBlock->setWordWrap(false);
        $docBlock->setLongDescription('@var \\' . $type->getName() . '|null wrapped object, if the proxy is initialized');
        $this->setDocBlock($docBlock);
        $this->setVisibility(self::VISIBILITY_PRIVATE);
    }
}
