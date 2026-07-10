<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\Authorization;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This is used by the ExceptionListener to translate an AccessDeniedException
 * to a Response object.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface AccessDeniedHandlerInterface
{
    /**
     * Handles an access denied failure.
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response;
}
