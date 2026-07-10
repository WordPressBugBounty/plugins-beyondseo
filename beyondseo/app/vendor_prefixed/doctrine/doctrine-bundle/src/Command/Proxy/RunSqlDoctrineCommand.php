<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;

use function trigger_deprecation;

/**
 * Execute a SQL query and output the results.
 *
 * @deprecated use BeyondSEODeps\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand instead
 */
class RunSqlDoctrineCommand extends RunSqlCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('doctrine:query:sql')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command executes the given SQL query and
outputs the results:

<info>php %command.full_name% "SELECT * FROM users"</info>
EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        beondseodeps_trigger_deprecation(
            'doctrine/doctrine-bundle',
            '2.2',
            'The "%s" (doctrine:query:sql) is deprecated, use dbal:run-sql command instead.',
            self::class,
        );

        return parent::execute($input, $output);
    }
}
