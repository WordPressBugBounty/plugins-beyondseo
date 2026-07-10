<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\MediaItems;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * @property MediaItem[] $elements;
 * @method MediaItem getByUniqueKey(string $uniqueKey)
 * @method MediaItem[] getElements()
 */
class MediaItems extends EntitySet
{
}