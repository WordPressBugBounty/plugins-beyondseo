<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ClearCache\CollectionRegionCommand;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;

/**
 * Command to clear a collection cache region.
 *
 * @deprecated use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ClearCache\CollectionRegionCommand instead
 */
class CollectionRegionDoctrineCommand extends CollectionRegionCommand
{
    use OrmProxyCommand;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('doctrine:cache:clear-collection-region');

        if ($this->getDefinition()->hasOption('em')) {
            return;
        }

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }
}
