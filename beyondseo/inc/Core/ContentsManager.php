<?php
declare( strict_types=1 );

namespace RankingCoach\Inc\Core;

if ( !defined('ABSPATH') ) {
    exit;
}

use RankingCoach\Inc\Traits\SingletonTrait;
use RankingCoach\Inc\Core\Admin\AdminManager;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use RankingCoach\Inc\Core\Settings\SettingsManager;

/**
 * Class ContentsManager
 */
class ContentsManager {

    use SingletonTrait;
    /**
     * Add the content assistant column to the posts and pages list.
     * @return void
     */
    public function addRankingCoachColumnOnTables(): void {
        add_filter('manage_posts_columns', [$this, 'registerAssistantColumn']);
        add_filter('manage_pages_columns', [$this, 'registerAssistantColumn']);
        add_action('manage_posts_custom_column', [$this, 'populateColumn'], 10, 2);
        add_action('manage_pages_custom_column', [$this, 'populateColumn'], 10, 2);
        register_deactivation_hook(__FILE__, [$this, 'cleanup']);
    }

    /**
     * Add the content assistant column to the posts and pages list.
     *
     * @param array $columns
     *
     * @return array
     */
    public function registerAssistantColumn(array $columns): array {
        /* translators: %s: brand name of the plugin */
        $columns['rc_assistant'] = sprintf(esc_html__('%s Assistant', 'beyondseo'), RANKINGCOACH_BRAND_NAME);
        return $columns;
    }

    /**
     * Populate the content assistant column with data.
     *
     * @param string $column_name
     * @param int $post_id
     *
     * @return void
     */
    public function populateColumn( string $column_name, int $post_id): void {
        $onboardingAdminPage = AdminManager::getPageUrl(AdminManager::PAGE_ONBOARDING);
        if ($column_name === 'rc_assistant') {

		    // Create a mounting point for React component, and pass data via attributes
            $postIdAttr = (string)$post_id;
		    echo sprintf("<div 
                class='rc-react-postcell' 
                id='rc-react-postcell-%s'
                data-id='%s'
                ></div>", esc_attr($postIdAttr), esc_attr($postIdAttr));
        }
    }

    /**
     * Cleanup the plugin on deactivation.
     *
     * @return void
     */
    public function cleanup(): void {
        remove_filter('manage_posts_columns', [$this, 'registerAssistantColumn']);
        remove_filter('manage_pages_columns', [$this, 'registerAssistantColumn']);
        remove_action('manage_posts_custom_column', [$this, 'populateColumn']);
        remove_action('manage_pages_custom_column', [$this, 'populateColumn']);
    }

	/**
	 * Get content insights for the site.
	 *
	 * @return array
	 */
	public function get_contents_data(): array {
		// Retrieve counts for different content types
		$total_posts = wp_count_posts()->publish ?? 0;
		$total_pages = wp_count_posts('page')->publish ?? 0;

		// Example custom post-type insights (adjust for actual CPTs)
		$custom_post_types = [];
		$post_types = get_post_types(['_builtin' => false]);
		foreach ($post_types as $post_type) {
			$custom_post_types[$post_type] = wp_count_posts($post_type)->publish ?? 0;
		}

		// Publishing trends
		$published_last_week = count(get_posts([
			'post_type' => 'post',
			'date_query' => [
				'after' => gmdate('Y-m-d', strtotime('-7 days'))
			],
			'posts_per_page' => -1,
			'fields' => 'ids'
		]));
		$published_last_month = count(get_posts([
			'post_type' => 'post',
			'date_query' => [
				'after' => gmdate('Y-m-d', strtotime('-30 days'))
			],
			'posts_per_page' => -1,
			'fields' => 'ids'
		]));

		// Content status breakdown
		$status_counts = [];
		$post_statuses = ['publish', 'draft', 'pending', 'trash'];
		foreach ($post_statuses as $status) {
			$status_counts[$status] = wp_count_posts()->{$status} ?? 0;
		}

		return [
			'total_posts' => $total_posts,
			'total_pages' => $total_pages,
			'custom_post_types' => $custom_post_types,
			'published_last_7_days' => $published_last_week,
			'published_last_30_days' => $published_last_month,
			'status_counts' => $status_counts
		];
	}

}
