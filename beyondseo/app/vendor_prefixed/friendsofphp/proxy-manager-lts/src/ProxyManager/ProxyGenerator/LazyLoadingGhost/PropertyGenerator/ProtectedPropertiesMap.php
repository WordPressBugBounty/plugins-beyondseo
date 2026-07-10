<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;

/**
 * Property that contains the protected instance lazy-loadable properties of an object
 */
class ProtectedPropertiesMap extends PropertyGenerator
{
    public const KEY_DEFAULT_VALUE = 'defaultValue';

    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Properties $properties)
    {
        parent::__construct(
            IdentifierSuffixer::getIdentifier('protectedProperties')
        );

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setStatic(true);
        $this->setDocBlock(
            '@var string[][] declaring class name of defined protected properties, indexed by property name'
        );
        $this->setDefaultValue($this->getMap($properties));
    }

    /** @return string[] */
    private function getMap(Properties $properties): array
    {
        $map = [];

        foreach ($properties->getProtectedProperties() as $property) {
            if (\PHP_VERSION_ID >= 80100 && $property->isReadOnly()) {
                continue;
            }

            $map[$property->getName()] = $property->getDeclaringClass()->getName();
        }

        return $map;
    }
}
