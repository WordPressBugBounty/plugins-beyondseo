<?php
        declare(strict_types=1);

        namespace BeyondSEO\Domain\Common\Repo\InternalDB\Models;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

        use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
        use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;

        #[ORM\Entity]
        #[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
        #[ORM\Table(name: 'roles_users')]
        class InternalDBRolesUserModel extends DoctrineModel
        {
            public const MODEL_ALIAS = 'roles_user';
            
        #[ORM\Id]
                #[ORM\Column(type: 'integer', name:'`user_id`')]
            public int $user_id;
            
            #[ORM\Id]
                #[ORM\Column(type: 'integer', name:'`role_id`')]
            public int $role_id;
            
            #[ORM\ManyToOne(targetEntity: InternalDBUserModel::class)]
            #[ORM\JoinColumn(name: '`user_id`', referencedColumnName: 'id')]
            public ?InternalDBUserModel $user;
            
            #[ORM\ManyToOne(targetEntity: InternalDBRoleModel::class)]
            #[ORM\JoinColumn(name: '`role_id`', referencedColumnName: 'id')]
            public ?InternalDBRoleModel $role;
            
            
        }