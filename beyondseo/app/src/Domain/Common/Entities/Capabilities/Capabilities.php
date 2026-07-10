<?php

namespace BeyondSEO\Domain\Common\Entities\Capabilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\NoRecursiveUpdate;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * @property BeyondSEO\Domain\Common\Entities\Capabilities\Capability[] $elements;
 * @method Capability getByUniqueKey(string $uniqueKey)
 * @method Capability[] getElements()
 */
#[NoRecursiveUpdate]
class Capabilities extends EntitySet
{

    /**
     * @var Capability[]
     */
    public array $elements = [];

    /**
     * Check if a capability exists in the set.
     */
    public function hasCapability(string $capability): bool
    {
        foreach ($this->elements as $element) {
            if ($element->getName() === $capability) {
                return true;
            }
        }
        return false;
    }
}