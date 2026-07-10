<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\WPAccount;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\InternalDBWPSetup;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

/**
 * Class WPSetup
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPSetup::class)]
class WPSetup extends ValueObject
{
    public const ONBOARDING_TYPE_APPLICATION = 'ONBOARDING_APPLICATION';
    public const ONBOARDING_TYPE_PLUGIN = 'ONBOARDING_PLUGIN';

    /** @var bool $isPluginOnboarded Is the internal onboarding completed */
    public bool $isPluginOnboarded = false;

    /** @var int|null $lastPluginUpdate The last internal onboarding update */
    public ?int $lastPluginUpdate = null;

    /** @var bool $isApplicationOnboarded Is the external onboarding completed */
    public bool $isApplicationOnboarded = false;

    /** @var int|null $lastApplicationUpdate The last external onboarding update */
    public ?int $lastApplicationUpdate = null;

    /** @var WPAccount|null $account The WordPress account */
    public ?WPAccount $account = null;
}