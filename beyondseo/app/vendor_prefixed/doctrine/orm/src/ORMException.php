<?php

declare (strict_types=1);
namespace BeyondSEODeps\Doctrine\ORM;

use BeyondSEODeps\Doctrine\Common\Cache\Cache as CacheDriver;
use BeyondSEODeps\Doctrine\ORM\Cache\Exception\InvalidResultCacheDriver;
use BeyondSEODeps\Doctrine\ORM\Cache\Exception\MetadataCacheNotConfigured;
use BeyondSEODeps\Doctrine\ORM\Cache\Exception\MetadataCacheUsesNonPersistentCache;
use BeyondSEODeps\Doctrine\ORM\Cache\Exception\QueryCacheNotConfigured;
use BeyondSEODeps\Doctrine\ORM\Cache\Exception\QueryCacheUsesNonPersistentCache;
use BeyondSEODeps\Doctrine\ORM\Exception\EntityManagerClosed;
use BeyondSEODeps\Doctrine\ORM\Exception\InvalidEntityRepository;
use BeyondSEODeps\Doctrine\ORM\Exception\InvalidHydrationMode;
use BeyondSEODeps\Doctrine\ORM\Exception\MismatchedEventManager;
use BeyondSEODeps\Doctrine\ORM\Exception\MissingIdentifierField;
use BeyondSEODeps\Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use BeyondSEODeps\Doctrine\ORM\Exception\NamedNativeQueryNotFound;
use BeyondSEODeps\Doctrine\ORM\Exception\NamedQueryNotFound;
use BeyondSEODeps\Doctrine\ORM\Exception\ProxyClassesAlwaysRegenerating;
use BeyondSEODeps\Doctrine\ORM\Exception\UnexpectedAssociationValue;
use BeyondSEODeps\Doctrine\ORM\Exception\UnknownEntityNamespace;
use BeyondSEODeps\Doctrine\ORM\Exception\UnrecognizedIdentifierFields;
use BeyondSEODeps\Doctrine\ORM\Persisters\Exception\CantUseInOperatorOnCompositeKeys;
use BeyondSEODeps\Doctrine\ORM\Persisters\Exception\InvalidOrientation;
use BeyondSEODeps\Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use BeyondSEODeps\Doctrine\ORM\Repository\Exception\InvalidFindByCall;
use BeyondSEODeps\Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall;
use BeyondSEODeps\Doctrine\ORM\Tools\Exception\NotSupported;
use Exception;
use function sprintf;
/**
 * Base exception class for all ORM exceptions.
 *
 * @deprecated Use Doctrine\ORM\Exception\ORMException for catch and instanceof
 */
class ORMException extends Exception
{
    /**
     * @deprecated Use Doctrine\ORM\Exception\MissingMappingDriverImplementation
     *
     * @return ORMException
     */
    public static function missingMappingDriverImpl()
    {
        return MissingMappingDriverImplementation::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\NamedQueryNotFound
     *
     * @param string $queryName
     *
     * @return ORMException
     */
    public static function namedQueryNotFound($queryName)
    {
        return NamedQueryNotFound::fromName($queryName);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\NamedNativeQueryNotFound
     *
     * @param string $nativeQueryName
     *
     * @return ORMException
     */
    public static function namedNativeQueryNotFound($nativeQueryName)
    {
        return NamedNativeQueryNotFound::fromName($nativeQueryName);
    }
    /**
     * @deprecated Use Doctrine\ORM\Persisters\Exception\UnrecognizedField
     *
     * @param string $field
     *
     * @return ORMException
     */
    public static function unrecognizedField($field)
    {
        return new UnrecognizedField(sprintf('Unrecognized field: %s', $field));
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\UnexpectedAssociationValue
     *
     * @param string $class
     * @param string $association
     * @param string $given
     * @param string $expected
     *
     * @return ORMException
     */
    public static function unexpectedAssociationValue($class, $association, $given, $expected)
    {
        return UnexpectedAssociationValue::create($class, $association, $given, $expected);
    }
    /**
     * @deprecated Use Doctrine\ORM\Persisters\Exception\InvalidOrientation
     *
     * @param string $className
     * @param string $field
     *
     * @return ORMException
     */
    public static function invalidOrientation($className, $field)
    {
        return InvalidOrientation::fromClassNameAndField($className, $field);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\EntityManagerClosed
     *
     * @return ORMException
     */
    public static function entityManagerClosed()
    {
        return EntityManagerClosed::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\InvalidHydrationMode
     *
     * @param string $mode
     *
     * @return ORMException
     */
    public static function invalidHydrationMode($mode)
    {
        return InvalidHydrationMode::fromMode($mode);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\MismatchedEventManager
     *
     * @return ORMException
     */
    public static function mismatchedEventManager()
    {
        return MismatchedEventManager::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall::onMissingParameter()
     *
     * @param string $methodName
     *
     * @return ORMException
     */
    public static function findByRequiresParameter($methodName)
    {
        return InvalidMagicMethodCall::onMissingParameter($methodName);
    }
    /**
     * @deprecated \BeyondSEODeps\Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall::becauseFieldNotFoundIn()
     *
     * @param string $entityName
     * @param string $fieldName
     * @param string $method
     *
     * @return ORMException
     */
    public static function invalidMagicCall($entityName, $fieldName, $method)
    {
        return InvalidMagicMethodCall::becauseFieldNotFoundIn($entityName, $fieldName, $method);
    }
    /**
     * @deprecated Use Doctrine\ORM\Repository\Exception\InvalidFindByCall::fromInverseSideUsage()
     *
     * @param string $entityName
     * @param string $associationFieldName
     *
     * @return ORMException
     */
    public static function invalidFindByInverseAssociation($entityName, $associationFieldName)
    {
        return InvalidFindByCall::fromInverseSideUsage($entityName, $associationFieldName);
    }
    /**
     * @deprecated Use Doctrine\ORM\Cache\Exception\InvalidResultCacheDriver
     *
     * @return ORMException
     */
    public static function invalidResultCacheDriver()
    {
        return InvalidResultCacheDriver::create();
    }
    /**
     * @deprecated \BeyondSEODeps\Doctrine\ORM\Tools\Exception\NotSupported
     *
     * @return ORMException
     */
    public static function notSupported()
    {
        return NotSupported::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Cache\Exception\QueryCacheNotConfigured
     *
     * @return ORMException
     */
    public static function queryCacheNotConfigured()
    {
        return QueryCacheNotConfigured::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Cache\Exception\MetadataCacheNotConfigured
     *
     * @return ORMException
     */
    public static function metadataCacheNotConfigured()
    {
        return MetadataCacheNotConfigured::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Cache\Exception\QueryCacheUsesNonPersistentCache
     *
     * @return ORMException
     */
    public static function queryCacheUsesNonPersistentCache(CacheDriver $cache)
    {
        return QueryCacheUsesNonPersistentCache::fromDriver($cache);
    }
    /**
     * @deprecated Use Doctrine\ORM\Cache\Exception\MetadataCacheUsesNonPersistentCache
     *
     * @return ORMException
     */
    public static function metadataCacheUsesNonPersistentCache(CacheDriver $cache)
    {
        return MetadataCacheUsesNonPersistentCache::fromDriver($cache);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\ProxyClassesAlwaysRegenerating
     *
     * @return ORMException
     */
    public static function proxyClassesAlwaysRegenerating()
    {
        return ProxyClassesAlwaysRegenerating::create();
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\UnknownEntityNamespace
     *
     * @param string $entityNamespaceAlias
     *
     * @return ORMException
     */
    public static function unknownEntityNamespace($entityNamespaceAlias)
    {
        return UnknownEntityNamespace::fromNamespaceAlias($entityNamespaceAlias);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\InvalidEntityRepository
     *
     * @param string $className
     *
     * @return ORMException
     */
    public static function invalidEntityRepository($className)
    {
        return InvalidEntityRepository::fromClassName($className);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\MissingIdentifierField
     *
     * @param string $className
     * @param string $fieldName
     *
     * @return ORMException
     */
    public static function missingIdentifierField($className, $fieldName)
    {
        return MissingIdentifierField::fromFieldAndClass($fieldName, $className);
    }
    /**
     * @deprecated Use Doctrine\ORM\Exception\UnrecognizedIdentifierFields
     *
     * @param string   $className
     * @param string[] $fieldNames
     *
     * @return ORMException
     */
    public static function unrecognizedIdentifierFields($className, $fieldNames)
    {
        return UnrecognizedIdentifierFields::fromClassAndFieldNames($className, $fieldNames);
    }
    /**
     * @deprecated Use Doctrine\ORM\Persisters\Exception\CantUseInOperatorOnCompositeKeys
     *
     * @return ORMException
     */
    public static function cantUseInOperatorOnCompositeKeys()
    {
        return CantUseInOperatorOnCompositeKeys::create();
    }
}