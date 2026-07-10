<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping;

/**
 * @deprecated Named queries won't be supported in ORM 3.
 *
 * @Annotation
 * @Target("CLASS")
 */
final class NamedQueries implements MappingAttribute
{
    /** @var array<\BeyondSEODeps\Doctrine\ORM\Mapping\NamedQuery> */
    public $value;
}
