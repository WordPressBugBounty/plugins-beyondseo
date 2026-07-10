<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Console\Helper;

use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Console\Descriptor\JsonDescriptor;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Console\Descriptor\MarkdownDescriptor;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Console\Descriptor\TextDescriptor;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Console\Descriptor\XmlDescriptor;
use BeyondSEODeps\Symfony\Component\Console\Helper\DescriptorHelper as BaseDescriptorHelper;
use BeyondSEODeps\Symfony\Component\HttpKernel\Debug\FileLinkFormatter;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class DescriptorHelper extends BaseDescriptorHelper
{
    public function __construct(?FileLinkFormatter $fileLinkFormatter = null)
    {
        $this
            ->register('txt', new TextDescriptor($fileLinkFormatter))
            ->register('xml', new XmlDescriptor())
            ->register('json', new JsonDescriptor())
            ->register('md', new MarkdownDescriptor())
        ;
    }
}
