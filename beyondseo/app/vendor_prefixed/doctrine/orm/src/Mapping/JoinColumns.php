<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class JoinColumns implements MappingAttribute
{
    /** @var array<\BeyondSEODeps\Doctrine\ORM\Mapping\JoinColumn> */
    public $value;
}
