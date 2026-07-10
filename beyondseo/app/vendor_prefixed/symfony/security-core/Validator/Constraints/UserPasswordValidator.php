<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Core\Validator\Constraints;

use BeyondSEODeps\Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use BeyondSEODeps\Symfony\Component\Validator\Constraint;
use BeyondSEODeps\Symfony\Component\Validator\ConstraintValidator;
use BeyondSEODeps\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use BeyondSEODeps\Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserPasswordValidator extends ConstraintValidator
{
    private $tokenStorage;
    private $hasherFactory;

    public function __construct(TokenStorageInterface $tokenStorage, PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->hasherFactory = $hasherFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $password, Constraint $constraint)
    {
        if (!$constraint instanceof UserPassword) {
            throw new UnexpectedTypeException($constraint, UserPassword::class);
        }

        if (null === $password || '' === $password) {
            $this->context->addViolation($constraint->message);

            return;
        }

        if (!\is_string($password)) {
            throw new UnexpectedTypeException($password, 'string');
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new ConstraintDefinitionException(sprintf('The "%s" class must implement the "%s" interface.', PasswordAuthenticatedUserInterface::class, get_debug_type($user)));
        }

        $hasher = $this->hasherFactory->getPasswordHasher($user);

        if (null === $user->getPassword() || !$hasher->verify($user->getPassword(), $password, $user instanceof LegacyPasswordAuthenticatedUserInterface ? $user->getSalt() : null)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
