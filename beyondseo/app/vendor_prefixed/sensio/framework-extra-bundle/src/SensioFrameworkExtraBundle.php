<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle;

use BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\AddParamConverterPass;
use BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\OptimizerPass;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use BeyondSEODeps\Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SensioFrameworkExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddParamConverterPass());
        $container->addCompilerPass(new OptimizerPass());
        $container->addCompilerPass(new AddExpressionLanguageProvidersPass());
    }
}
