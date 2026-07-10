<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\IdentifierSuffixer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;

/**
 * Map of public properties that exist in the class being proxied
 */
class PublicPropertiesMap extends PropertyGenerator
{
    /** @var array<string, bool> */
    private $publicProperties = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(Properties $properties, bool $skipReadOnlyProperties = false)
    {
        parent::__construct(IdentifierSuffixer::getIdentifier('publicProperties'));

        foreach ($properties->getPublicProperties() as $publicProperty) {
            if ($skipReadOnlyProperties && \PHP_VERSION_ID >= 80100 && $publicProperty->isReadOnly()) {
                continue;
            }

            $this->publicProperties[$publicProperty->getName()] = true;
        }

        $this->setDefaultValue($this->publicProperties);
        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setStatic(true);
        $this->setDocBlock('@var bool[] map of public properties of the parent class');
    }

    public function isEmpty(): bool
    {
        return ! $this->publicProperties;
    }
}
