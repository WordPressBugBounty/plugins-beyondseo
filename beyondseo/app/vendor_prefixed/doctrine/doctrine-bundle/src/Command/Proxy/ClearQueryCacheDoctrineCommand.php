<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;

/**
 * Command to clear the query cache of the various cache drivers.
 *
 * @deprecated use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand instead
 */
class ClearQueryCacheDoctrineCommand extends QueryCommand
{
    use OrmProxyCommand;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('doctrine:cache:clear-query')
            ->setDescription('Clears all query cache for an entity manager');

        if ($this->getDefinition()->hasOption('em')) {
            return;
        }

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }
}
