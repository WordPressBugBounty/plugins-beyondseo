<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities\Domains;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * @property Domain[] $elements;
 * @method Domain getByUniqueKey(string $uniqueKey)
 * @method Domain[] getElements()
 * @method Domain first()
 */
class Domains extends EntitySet
{

}