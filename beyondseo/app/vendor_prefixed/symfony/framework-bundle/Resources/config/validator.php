<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\CacheWarmer\ValidatorCacheWarmer;
use BeyondSEODeps\Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use BeyondSEODeps\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\EmailValidator;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\ExpressionValidator;
use BeyondSEODeps\Symfony\Component\Validator\Constraints\NotCompromisedPasswordValidator;
use BeyondSEODeps\Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use BeyondSEODeps\Symfony\Component\Validator\Mapping\Loader\PropertyInfoLoader;
use BeyondSEODeps\Symfony\Component\Validator\Validation;
use BeyondSEODeps\Symfony\Component\Validator\Validator\ValidatorInterface;
use BeyondSEODeps\Symfony\Component\Validator\ValidatorBuilder;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('validator.mapping.cache.file', param('kernel.cache_dir').'/validation.php');

    $container->services()
        ->set('validator', ValidatorInterface::class)
            ->factory([service('validator.builder'), 'getValidator'])
        ->alias(ValidatorInterface::class, 'validator')

        ->set('validator.builder', ValidatorBuilder::class)
            ->factory([Validation::class, 'createValidatorBuilder'])
            ->call('setConstraintValidatorFactory', [
                service('validator.validator_factory'),
            ])
            ->call('setTranslator', [
                service('translator')->ignoreOnInvalid(),
            ])
            ->call('setTranslationDomain', [
                param('validator.translation_domain'),
            ])
        ->alias('validator.mapping.class_metadata_factory', 'validator')

        ->set('validator.mapping.cache_warmer', ValidatorCacheWarmer::class)
            ->args([
                service('validator.builder'),
                param('validator.mapping.cache.file'),
            ])
            ->tag('kernel.cache_warmer')

        ->set('validator.mapping.cache.adapter', PhpArrayAdapter::class)
            ->factory([PhpArrayAdapter::class, 'create'])
            ->args([
                param('validator.mapping.cache.file'),
                service('cache.validator'),
            ])

        ->set('validator.validator_factory', ContainerConstraintValidatorFactory::class)
            ->args([
                abstract_arg('Constraint validators locator'),
            ])

        ->set('validator.expression', ExpressionValidator::class)
            ->args([service('validator.expression_language')->nullOnInvalid()])
            ->tag('validator.constraint_validator', [
                'alias' => 'validator.expression',
            ])

        ->set('validator.expression_language', ExpressionLanguage::class)
            ->args([service('cache.validator_expression_language')->nullOnInvalid()])

        ->set('cache.validator_expression_language')
            ->parent('cache.system')
            ->tag('cache.pool')

        ->set('validator.email', EmailValidator::class)
            ->args([
                abstract_arg('Default mode'),
            ])
            ->tag('validator.constraint_validator', [
                'alias' => EmailValidator::class,
            ])

        ->set('validator.not_compromised_password', NotCompromisedPasswordValidator::class)
            ->args([
                service('http_client')->nullOnInvalid(),
                param('kernel.charset'),
                false,
            ])
            ->tag('validator.constraint_validator', [
                'alias' => NotCompromisedPasswordValidator::class,
            ])

        ->set('validator.property_info_loader', PropertyInfoLoader::class)
            ->args([
                service('property_info'),
                service('property_info'),
                service('property_info'),
            ])
            ->tag('validator.auto_mapper')
    ;
};
