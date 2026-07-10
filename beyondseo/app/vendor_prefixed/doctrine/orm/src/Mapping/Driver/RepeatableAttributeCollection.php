<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping\Driver;

use ArrayObject;
use BeyondSEODeps\Doctrine\ORM\Mapping\Annotation;

/**
 * @template-extends ArrayObject<int, T>
 * @template T of Annotation
 */
final class RepeatableAttributeCollection extends ArrayObject
{
}
