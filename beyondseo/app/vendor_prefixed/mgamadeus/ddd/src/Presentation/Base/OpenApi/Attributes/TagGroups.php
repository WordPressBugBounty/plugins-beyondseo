<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes;

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;
use BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes\ExposePropertyInsteadOfClass;

/**
 * @property TagGroup[] $elements
 * @property TagGroup[] elementsByUniqueKey
 * @method TagGroup getByUniqueKey(string $uniqueKey)
 * @method TagGroup[] getElements()
 */
#[ExposePropertyInsteadOfClass('elements')]
class TagGroups extends ObjectSet
{
}