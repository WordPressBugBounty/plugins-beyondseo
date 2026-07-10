<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;

/**
 * Property that contains the initializer for a lazy object
 */
class PrivatePropertiesMap extends PropertyGenerator
{
    public const KEY_DEFAULT_VALUE = 'defaultValue';

    /** @var list<string> */
    private $readOnlyPropertyNames = [];

    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Properties $properties)
    {
        parent::__construct(
            IdentifierSuffixer::getIdentifier('privateProperties')
        );

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setStatic(true);
        $this->setDocBlock(
            '@var array[][] visibility and default value of defined properties, indexed by property name and class name'
        );
        $this->setDefaultValue($this->getMap($properties));
    }

    /**
     * @return list<string>
     */
    public function getReadOnlyPropertyNames(): array
    {
        return $this->readOnlyPropertyNames;
    }

    /**
     * @return array<string, array<class-string, bool>>
     */
    private function getMap(Properties $properties): array
    {
        $map = [];

        foreach ($properties->getInstanceProperties() as $property) {
            if (\PHP_VERSION_ID >= 80100 && $property->isReadOnly()) {
                $this->readOnlyPropertyNames[] = $property->getName();
            } elseif (! $property->isPrivate()) {
                continue;
            }

            $map[$property->getName()][$property->getDeclaringClass()->getName()] = true;
        }

        return $map;
    }
}
