<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\Controller;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\User\UserInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Supports the argument type of {@see UserInterface}.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class UserValueResolver implements ArgumentValueResolverInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // with the attribute, the type can be any UserInterface implementation
        // otherwise, the type must be UserInterface
        if (UserInterface::class !== $argument->getType() && !$argument->getAttributes(CurrentUser::class, ArgumentMetadata::IS_INSTANCEOF)) {
            return false;
        }

        $token = $this->tokenStorage->getToken();

        return $token instanceof TokenInterface;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield $this->tokenStorage->getToken()->getUser();
    }
}
