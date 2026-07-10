<?php

namespace BeyondSEO\Domain\Common\Entities\Persons;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * @property Person[] $elements;
 * @method Person first()
 * @method Person getByUniqueKey(string $uniqueKey)
 * @method Person[] getElements()
 */
class Persons extends ObjectSet
{
}