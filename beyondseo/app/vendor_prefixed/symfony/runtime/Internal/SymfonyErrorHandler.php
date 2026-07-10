<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Runtime\Internal;

use BeyondSEODeps\Symfony\Component\ErrorHandler\BufferingLogger;
use BeyondSEODeps\Symfony\Component\ErrorHandler\DebugClassLoader;
use BeyondSEODeps\Symfony\Component\ErrorHandler\ErrorHandler;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class SymfonyErrorHandler
{
    public static function register(bool $debug): void
    {
        BasicErrorHandler::register($debug);

        if (class_exists(ErrorHandler::class)) {
            DebugClassLoader::enable();
            restore_error_handler();
            ErrorHandler::register(new ErrorHandler(new BufferingLogger(), $debug));
        }
    }
}
