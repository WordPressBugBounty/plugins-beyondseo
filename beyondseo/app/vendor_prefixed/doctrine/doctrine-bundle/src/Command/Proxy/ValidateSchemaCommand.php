<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand as DoctrineValidateSchemaCommand;
use BeyondSEODeps\Symfony\Component\Console\Input\InputOption;

/**
 * Command to run Doctrine ValidateSchema() on the current mappings.
 *
 * @deprecated use BeyondSEODeps\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand instead
 */
class ValidateSchemaCommand extends DoctrineValidateSchemaCommand
{
    use OrmProxyCommand;

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('doctrine:schema:validate');

        if ($this->getDefinition()->hasOption('em')) {
            return;
        }

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }
}
