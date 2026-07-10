<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Types;

use BeyondSEODeps\Doctrine\DBAL\ParameterType;
use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;

final class AsciiStringType extends StringType
{
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getAsciiStringTypeDeclarationSQL($column);
    }

    public function getBindingType(): int
    {
        return ParameterType::ASCII;
    }

    public function getName(): string
    {
        return Types::ASCII_STRING;
    }
}
