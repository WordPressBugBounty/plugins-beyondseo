<?php

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Settings\Setting;
use BeyondSEO\Domain\Integrations\WordPress\Common\Repo\InternalDB\Accounts\Legacy\Settings\InternalDBWPLegacyAccountSetting;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use Exception;

/**
 * WordPress Account Setting
 */
#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, InternalDBWPLegacyAccountSetting::class)]
class WPLegacyAccountSetting extends Setting
{
    #============================================
    # region Attributes
    #============================================
    /** @var int|null $umetaId The ID of the user meta. */
    public ?int $umetaId = null;

    /** @var int $accountId The ID of the user account. */
    public int $accountId;

    /** @var string $metaKey The key of the user meta. */
    public string $metaKey;

    /** @var object $metaValue The value of the user meta. */
    public object $metaValue;

    # endregion
    #============================================

    #============================================
    # region Constructor and Methods
    #============================================
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieves a unique key for the task.
     * @return string
     * @throws Exception
     */
    public function uniqueKey(): string
    {
        try {
            return self::uniqueKeyStatic(parent::uniqueKey() . '_' . spl_object_hash($this));
        } catch (Exception $e) {
            /* translators: %s is the error message */
            throw new Exception(sprintf(__('Failed to generate unique key: %s', 'beyondseo'), $e->getMessage())); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }
    }
    # endregion
    #============================================
}