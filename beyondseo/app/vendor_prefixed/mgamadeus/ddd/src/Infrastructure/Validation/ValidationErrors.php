<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Validation;

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * @method ValidationError first()
 * @method ValidationError getByUniqueKey(string $uniqueKey)
 * @method ValidationError[] getElements()
 * @property ValidationError[] $elements;
 */
class ValidationErrors extends ObjectSet
{

}