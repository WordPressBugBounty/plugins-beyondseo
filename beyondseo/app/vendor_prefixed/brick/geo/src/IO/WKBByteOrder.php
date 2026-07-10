<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\IO;

enum WKBByteOrder: int
{
    case BIG_ENDIAN = 0;
    case LITTLE_ENDIAN = 1;
}
