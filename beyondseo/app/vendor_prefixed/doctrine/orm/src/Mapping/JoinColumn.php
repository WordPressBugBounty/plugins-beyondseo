<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping;

use Attribute;
use BeyondSEODeps\Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"PROPERTY","ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class JoinColumn implements MappingAttribute
{
    use JoinColumnProperties;
}
