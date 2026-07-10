<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bridge\Doctrine\Validator;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadataInfo;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException as OrmMappingException;
use BeyondSEODeps\Doctrine\Persistence\Mapping\MappingException;
use BeyondSEODeps\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use BeyondSEODeps\Symfony\Component\PropertyInfo\Type;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\Length;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\Valid;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\AutoMappingStrategy;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\ClassMetadata;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\Loader\AutoMappingTrait;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

/**
 * Guesses and loads the appropriate constraints using Doctrine's metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class DoctrineLoader implements LoaderInterface
{
    use AutoMappingTrait;

    private $entityManager;
    private ?string $classValidatorRegexp;

    public function __construct(EntityManagerInterface $entityManager, ?string $classValidatorRegexp = null)
    {
        $this->entityManager = $entityManager;
        $this->classValidatorRegexp = $classValidatorRegexp;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        $className = $metadata->getClassName();
        try {
            $doctrineMetadata = $this->entityManager->getClassMetadata($className);
        } catch (MappingException|OrmMappingException $exception) {
            return false;
        }

        if (!$doctrineMetadata instanceof ClassMetadataInfo) {
            return false;
        }

        $loaded = false;
        $enabledForClass = $this->isAutoMappingEnabledForClass($metadata, $this->classValidatorRegexp);

        /* Available keys:
           - type
           - scale
           - length
           - unique
           - nullable
           - precision
         */
        $existingUniqueFields = $this->getExistingUniqueFields($metadata);

        // Type and nullable aren't handled here, use the PropertyInfo Loader instead.
        foreach ($doctrineMetadata->fieldMappings as $mapping) {
            $enabledForProperty = $enabledForClass;
            $lengthConstraint = null;
            foreach ($metadata->getPropertyMetadata($mapping['fieldName']) as $propertyMetadata) {
                // Enabling or disabling auto-mapping explicitly always takes precedence
                if (AutoMappingStrategy::DISABLED === $propertyMetadata->getAutoMappingStrategy()) {
                    continue 2;
                }
                if (AutoMappingStrategy::ENABLED === $propertyMetadata->getAutoMappingStrategy()) {
                    $enabledForProperty = true;
                }

                foreach ($propertyMetadata->getConstraints() as $constraint) {
                    if ($constraint instanceof Length) {
                        $lengthConstraint = $constraint;
                    }
                }
            }

            if (!$enabledForProperty) {
                continue;
            }

            if (true === ($mapping['unique'] ?? false) && !isset($existingUniqueFields[$mapping['fieldName']])) {
                $metadata->addConstraint(new UniqueEntity(['fields' => $mapping['fieldName']]));
                $loaded = true;
            }

            if (null === ($mapping['length'] ?? null) || null !== ($mapping['enumType'] ?? null) || !\in_array($mapping['type'], ['string', 'text'], true)) {
                continue;
            }

            if (null === $lengthConstraint) {
                if (isset($mapping['originalClass']) && !str_contains($mapping['declaredField'], '.')) {
                    $metadata->addPropertyConstraint($mapping['declaredField'], new Valid());
                    $loaded = true;
                } elseif (property_exists($className, $mapping['fieldName'])) {
                    $metadata->addPropertyConstraint($mapping['fieldName'], new Length(['max' => $mapping['length']]));
                    $loaded = true;
                }
            } elseif (null === $lengthConstraint->max) {
                // If a Length constraint exists and no max length has been explicitly defined, set it
                $lengthConstraint->max = $mapping['length'];
            }
        }

        return $loaded;
    }

    private function getExistingUniqueFields(ClassMetadata $metadata): array
    {
        $fields = [];
        foreach ($metadata->getConstraints() as $constraint) {
            if (!$constraint instanceof UniqueEntity) {
                continue;
            }

            if (\is_string($constraint->fields)) {
                $fields[$constraint->fields] = true;
            } elseif (\is_array($constraint->fields) && 1 === \count($constraint->fields)) {
                $fields[$constraint->fields[0]] = true;
            }
        }

        return $fields;
    }
}
