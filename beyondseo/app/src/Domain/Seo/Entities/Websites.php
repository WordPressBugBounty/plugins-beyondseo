<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * @property Website[] $elements;
 * @method Website getByUniqueKey(string $uniqueKey)
 * @method Website[] getElements()
 */
class Websites extends EntitySet
{

}