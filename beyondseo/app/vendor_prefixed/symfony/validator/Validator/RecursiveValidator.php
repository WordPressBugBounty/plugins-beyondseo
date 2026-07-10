<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Validator\Validator;

use BeyondSEODeps\Symfony\Component\Validator\Constraint;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\GroupSequence;
use BeyondSEODeps\Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use BeyondSEODeps\Symfony\Component\Validator\ConstraintViolationListInterface;
use BeyondSEODeps\Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use BeyondSEODeps\Symfony\Component\Validator\Context\ExecutionContextInterface;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\MetadataInterface;
use BeyondSEODeps\Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * Recursive implementation of {@link ValidatorInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class RecursiveValidator implements ValidatorInterface
{
    protected $contextFactory;
    protected $metadataFactory;
    protected $validatorFactory;
    protected $objectInitializers;

    /**
     * Creates a new validator.
     *
     * @param ObjectInitializerInterface[] $objectInitializers The object initializers
     */
    public function __construct(ExecutionContextFactoryInterface $contextFactory, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, array $objectInitializers = [])
    {
        $this->contextFactory = $contextFactory;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->objectInitializers = $objectInitializers;
    }

    /**
     * {@inheritdoc}
     */
    public function startContext(mixed $root = null): ContextualValidatorInterface
    {
        return new RecursiveContextualValidator(
            $this->contextFactory->createContext($this, $root),
            $this->metadataFactory,
            $this->validatorFactory,
            $this->objectInitializers
        );
    }

    /**
     * {@inheritdoc}
     */
    public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
    {
        return new RecursiveContextualValidator(
            $context,
            $this->metadataFactory,
            $this->validatorFactory,
            $this->objectInitializers
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor(mixed $object): MetadataInterface
    {
        return $this->metadataFactory->getMetadataFor($object);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor(mixed $object): bool
    {
        return $this->metadataFactory->hasMetadataFor($object);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint|array|null $constraints = null, string|GroupSequence|array|null $groups = null): ConstraintViolationListInterface
    {
        return $this->startContext($value)
            ->validate($value, $constraints, $groups)
            ->getViolations();
    }

    /**
     * {@inheritdoc}
     */
    public function validateProperty(object $object, string $propertyName, string|GroupSequence|array|null $groups = null): ConstraintViolationListInterface
    {
        return $this->startContext($object)
            ->validateProperty($object, $propertyName, $groups)
            ->getViolations();
    }

    /**
     * {@inheritdoc}
     */
    public function validatePropertyValue(object|string $objectOrClass, string $propertyName, mixed $value, string|GroupSequence|array|null $groups = null): ConstraintViolationListInterface
    {
        // If a class name is passed, take $value as root
        return $this->startContext(\is_object($objectOrClass) ? $objectOrClass : $value)
            ->validatePropertyValue($objectOrClass, $propertyName, $value, $groups)
            ->getViolations();
    }
}
