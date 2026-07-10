<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Command;

use BeyondSEODeps\Symfony\Component\Cache\PruneableInterface;
use BeyondSEODeps\Symfony\Component\Console\Attribute\AsCommand;
use BeyondSEODeps\Symfony\Component\Console\Command\Command;
use BeyondSEODeps\Symfony\Component\Console\Input\InputInterface;
use BeyondSEODeps\Symfony\Component\Console\Output\OutputInterface;
use BeyondSEODeps\Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Cache pool pruner command.
 *
 * @author Rob Frawley 2nd <rmf@src.run>
 */
#[AsCommand(name: 'cache:pool:prune', description: 'Prune cache pools')]
final class CachePoolPruneCommand extends Command
{
    private iterable $pools;

    /**
     * @param iterable<mixed, PruneableInterface> $pools
     */
    public function __construct(iterable $pools)
    {
        parent::__construct();

        $this->pools = $pools;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command deletes all expired items from all pruneable pools.

    %command.full_name%
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->pools as $name => $pool) {
            $io->comment(sprintf('Pruning cache pool: <info>%s</info>', $name));
            $pool->prune();
        }

        $io->success('Successfully pruned cache pool(s).');

        return 0;
    }
}
