<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;

use function trigger_deprecation;

/**
 * @internal
 * @deprecated
 */
trait OrmProxyCommand
{
    private ?EntityManagerProvider $entityManagerProvider;

    public function __construct(?EntityManagerProvider $entityManagerProvider = null)
    {
        parent::__construct($entityManagerProvider);

        $this->entityManagerProvider = $entityManagerProvider;

        beondseodeps_trigger_deprecation(
            'doctrine/doctrine-bundle',
            '2.8',
            'Class "%s" is deprecated. Use "%s" instead.',
            self::class,
            parent::class,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->entityManagerProvider) {
            DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        }

        return parent::execute($input, $output);
    }
}
