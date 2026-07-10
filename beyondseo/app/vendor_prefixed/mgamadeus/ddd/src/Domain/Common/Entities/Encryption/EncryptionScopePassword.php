<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\Encryption;

use BeyondSEODeps\DDD\Domain\Common\Entities\Roles\Role;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\RolesRequiredForUpdate;
use BeyondSEODeps\DDD\Domain\Base\Entities\ChangeHistory\ChangeHistoryTrait;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Database\DatabaseIndex;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Encryption\DBEncryptionScopePassword;
use BeyondSEODeps\DDD\Domain\Common\Services\EncryptionScopesService;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\UnauthorizedException;
use BeyondSEODeps\DDD\Infrastructure\Libs\Encrypt;

/**
 * @method EncryptionScopes getParent()
 * @property EncryptionScopes $parent
 * @method static EncryptionScopesService getService()
 * @method static DBEncryptionScopePassword getRepoClassInstance(?string $repoType = null)
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBEncryptionScopePassword::class)]
#[QueryOptions]
#[RolesRequiredForUpdate(Role::ADMIN)]
class EncryptionScopePassword extends Entity
{
    use QueryOptionsTrait, ChangeHistoryTrait;

    public const COOKIE_NAME = 'encryptionPassword';

    /** @var int The id of the EncryptionScope */
    public int $encryptionScopeId;

    /** @var EncryptionScope The associated EncryptedScope */
    #[LazyLoad(LazyLoadRepo::DB, addAsParent: true)]
    public EncryptionScope $encryptionScope;

    /** @var string The hash of the password for easy retrieval */
    #[DatabaseIndex(indexType: DatabaseIndex::TYPE_INDEX)]
    public string $passwordHash;

    /** @var string The Password of the Encrypted Scope, encrypted with the password */
    public string $encryptionScopePassword;

    /**
     * Updates the EncryptionScopePassword, saves passwordHash and encrypts its encryptionScopePassword using $encryptionPassword
     * @param string $encryptionPassword
     * @param string $scopePassword
     * @return $this|null
     */
    public function updateUsingPassword(string $encryptionPassword, string $scopePassword): ?static
    {
        $this->passwordHash = Encrypt::hashWithSalt($encryptionPassword);
        $this->encryptionScopePassword = Encrypt::encrypt($scopePassword, $encryptionPassword);
        return parent::update();
    }

    /**
     * Sets encryptionScopePassword to decrypted version using $decryptionPassword
     * @param string $decryptionPassword
     * @return void
     * @throws UnauthorizedException
     */
    public function decryptScopePassword(string $decryptionPassword): void
    {
        $encryptionScopePassword = Encrypt::decrypt($this->encryptionScopePassword, $decryptionPassword);
        if (!$encryptionScopePassword) {
            throw new UnauthorizedException('Invalid decryption password');
        }
        $this->encryptionScopePassword = $encryptionScopePassword;
    }
}
