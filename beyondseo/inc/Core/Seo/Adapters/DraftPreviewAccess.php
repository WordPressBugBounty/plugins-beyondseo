<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Seo\Adapters;

if (!defined('ABSPATH')) {
    exit;
}

use WP_Post;
use WP_Query;

/**
 * Lets the analysis loopback fetch render a non-published post through a
 * short-lived preview token instead of changing the post status.
 */
class DraftPreviewAccess
{
    public const QUERY_ARG = 'rc_preview';

    private const TOKEN_LENGTH = 32;

    /** @var array<int, string> Tokens issued in this request, keyed by post ID */
    private static array $issuedTokens = [];

    /**
     * Preview URL for a non-published post, valid for a few minutes.
     *
     * @param int $postId The post ID
     * @return string The URL, or an empty string when the post has no permalink
     */
    public static function previewUrl(int $postId): string
    {
        $permalink = get_permalink($postId);
        if (!$permalink) {
            return '';
        }

        // One token per request keeps the loopback caches (keyed by URL) effective.
        if (!isset(self::$issuedTokens[$postId])) {
            $token = wp_generate_password(self::TOKEN_LENGTH, false);
            // Keyed by token, so concurrent analyses of one post never invalidate each other.
            set_transient(self::transientKey($token), $postId, 5 * MINUTE_IN_SECONDS);
            self::$issuedTokens[$postId] = $token;
        }

        // preview=true keeps core from canonical-redirecting the plain permalink.
        return add_query_arg([
            'preview' => 'true',
            self::QUERY_ARG => self::$issuedTokens[$postId],
        ], $permalink);
    }

    public function register(): void
    {
        add_filter('posts_results', [$this, 'exposeForPreview'], 10, 2);
    }

    /**
     * @param WP_Post[] $posts
     * @return WP_Post[]
     */
    public function exposeForPreview(array $posts, WP_Query $query): array
    {
        if (is_admin() || !$query->is_main_query() || !$query->is_singular() || count($posts) !== 1) {
            return $posts;
        }

        $post = $posts[0];
        if (!$post instanceof WP_Post || $post->post_status === 'publish' || !$this->hasValidToken($post->ID)) {
            return $posts;
        }

        // Core empties the result for cookie-less requests unless the status is public; a copy keeps the object cache truthful.
        $preview = clone $post;
        $preview->post_status = 'publish';
        $posts[0] = $preview;

        nocache_headers();
        defined('DONOTCACHEPAGE') || define('DONOTCACHEPAGE', true);

        return $posts;
    }

    private function hasValidToken(int $postId): bool
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the token is the credential
        $token = isset($_GET[self::QUERY_ARG]) ? sanitize_text_field(wp_unslash($_GET[self::QUERY_ARG])) : '';
        if (strlen($token) !== self::TOKEN_LENGTH) {
            return false;
        }

        return (int) get_transient(self::transientKey($token)) === $postId;
    }

    private static function transientKey(string $token): string
    {
        return 'rankingcoach_preview_' . substr(wp_hash($token), 0, 32);
    }
}
