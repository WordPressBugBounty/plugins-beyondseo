<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Common\Repo\InternalDB\Models;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;

/**
 * Represents a LegacyDBUserModel
 */
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'users')]
class InternalDBUserModel extends DoctrineModel
{
    public const MODEL_ALIAS = 'users';

    #[ORM\Id]
    #[ORM\Column(name: 'ID', type: 'bigint', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $ID;

    #[ORM\Column(name: 'user_login', type: 'string', length: 60)]
    public string $user_login;

    #[ORM\Column(name: 'user_pass', type: 'string', length: 255)]
    public string $user_pass;

    #[ORM\Column(name: 'user_nicename', type: 'string', length: 50)]
    public string $user_nicename;

    #[ORM\Column(name: 'user_email', type: 'string', length: 100)]
    public string $user_email;

    #[ORM\Column(name: 'user_url', type: 'string', length: 100)]
    public string $user_url;

    #[ORM\Column(name: 'user_registered', type: 'string')]
    public string $user_registered;

    #[ORM\Column(name: 'user_activation_key', type: 'string', length: 255)]
    public string $user_activation_key;

    #[ORM\Column(name: 'user_status', type: 'integer', options: ['unsigned' => true])]
    public int $user_status;

    #[ORM\Column(name: 'display_name', type: 'string', length: 250)]
    public string $display_name;

    /** @var InternalDBUserMetaModel[] */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: InternalDBUserMetaModel::class)]
    public array|PersistentCollection $accountSettings;

    /** @var InternalDBContentModel[] */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: InternalDBContentModel::class)]
    public array|PersistentCollection $author;
}