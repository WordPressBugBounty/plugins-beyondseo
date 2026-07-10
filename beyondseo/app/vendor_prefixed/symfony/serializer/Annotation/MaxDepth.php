<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Serializer\Annotation;

use BeyondSEODeps\Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * Annotation class for @MaxDepth().
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class MaxDepth
{
    public function __construct(private int $maxDepth)
    {
        if ($maxDepth <= 0) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be a positive integer.', static::class));
        }
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }
}
