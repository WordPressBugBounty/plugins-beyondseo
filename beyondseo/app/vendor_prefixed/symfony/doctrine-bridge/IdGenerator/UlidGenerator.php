<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bridge\Doctrine\IdGenerator;

use BeyondSEODeps\Doctrine\ORM\EntityManager;
use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Component\Uid\Ulid;

final class UlidGenerator extends AbstractIdGenerator
{
    private $factory;

    public function __construct(?UlidFactory $factory = null)
    {
        $this->factory = $factory;
    }

    /**
     * doctrine/orm < 2.11 BC layer.
     */
    public function generate(EntityManager $em, $entity): Ulid
    {
        return $this->generateId($em, $entity);
    }

    public function generateId(EntityManagerInterface $em, $entity): Ulid
    {
        if ($this->factory) {
            return $this->factory->create();
        }

        return new Ulid();
    }
}
