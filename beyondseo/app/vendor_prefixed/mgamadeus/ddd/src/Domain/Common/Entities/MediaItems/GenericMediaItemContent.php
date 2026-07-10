<?php

namespace BeyondSEODeps\DDD\Domain\Common\Entities\MediaItems;

use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

/**
 * @method MediaItem getParent()
 */
class GenericMediaItemContent extends ValueObject
{
    use MediaItemContentTrait;
}
