<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

use RankingCoach\Inc\Core\Initializers\Hooks;

/**
 * Who may use the plugin.
 *
 * Site-wide features (admin pages, settings, account, site-wide REST routes) require MANAGE, a
 * capability resolved per request through `map_meta_cap`: administrators and editors have it,
 * authors do not. Nothing is written to the roles, so it works on new and existing installs and
 * on multisite alike, and a site can change the underlying capability through a filter.
 *
 * Post-level features (meta box, list quick editor, their REST routes) follow WordPress' own
 * `edit_post`, so authors can still work on the SEO of their own posts.
 */
final class Capabilities {

    /** Capability of the plugin's admin pages, settings and site-wide REST routes. */
    public const MANAGE = 'rankingcoach_manage';

    /** Capability MANAGE resolves to by default: editors and administrators have it, authors do not. */
    private const MANAGE_DEFAULT = 'edit_others_posts';

    /**
     * Makes MANAGE resolvable. Called from the plugin bootstrap before any context gate, because
     * REST permission callbacks (backend sync with application passwords, cron, WP-CLI) check it too.
     */
    public static function register(): void {
        add_filter('map_meta_cap', [self::class, 'mapManageCapability'], 10, 3);
    }

    /**
     * Resolves MANAGE to a real capability of the user (`map_meta_cap` filter).
     *
     * Administrators always pass, even when a role plugin stripped the editor-level capability
     * from their role. The default can be changed with Hooks::RANKINGCOACH_FILTER_MANAGE_CAPABILITY.
     *
     * @param string[] $caps Capabilities required so far.
     * @param string $cap Capability being checked.
     * @param int $userId User being checked.
     * @return string[]
     */
    public static function mapManageCapability(array $caps, string $cap, int $userId): array {
        if ($cap !== self::MANAGE) {
            return $caps;
        }

        if ($userId > 0 && user_can($userId, 'manage_options')) {
            return ['manage_options'];
        }

        return [(string) apply_filters(Hooks::RANKINGCOACH_FILTER_MANAGE_CAPABILITY, self::MANAGE_DEFAULT)];
    }

    /**
     * Whether the current user may read and change the SEO data of a post.
     *
     * Unknown posts fail `edit_post`, so no separate existence check is needed. Sites can tighten
     * or loosen the rule through Hooks::RANKINGCOACH_FILTER_USER_CAN_EDIT_POST_SEO.
     *
     * @param int $postId
     * @return bool
     */
    public static function canEditPostSeo(int $postId): bool {
        $can = $postId > 0 && current_user_can('edit_post', $postId);

        return (bool) apply_filters(Hooks::RANKINGCOACH_FILTER_USER_CAN_EDIT_POST_SEO, $can, $postId);
    }
}
