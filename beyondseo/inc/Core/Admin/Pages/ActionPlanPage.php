<?php
declare( strict_types=1 );

namespace RankingCoach\Inc\Core\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RankingCoach\Inc\Core\Admin\AdminManager;
use RankingCoach\Inc\Core\Admin\AdminPage;
use RankingCoach\Inc\Core\Frontend\ViteApp\ReactApp;
use RankingCoach\Inc\Core\Helpers\JavaScriptHelper;
use RankingCoach\Inc\Traits\SingletonTrait;

/**
 * Action Plan page (React): the page issues found by the site check, with fixes per page.
 * Like the setup wizard it is not guarded by the channel flow: it works without an account.
 * @method ActionPlanPage getInstance(): static
 */
class ActionPlanPage extends AdminPage {

    use SingletonTrait;

    public string $name = 'actionPlan';

    public static AdminManager|null $managerInstance = null;

    public function __construct() {
        add_action('current_screen', function($screen) {
            if (!is_object($screen) || !isset($screen->id)) {
                return;
            }

            if (
                $screen->base === RANKINGCOACH_BRAND_SLUG . '_page_rankingcoach-' . $this->name ||
                $screen->base === 'admin_page_rankingcoach-' . $this->name
            ) {
                ReactApp::get(['actionPlan']);
            }
        });
        parent::__construct();
    }

    public function page_name(): string
    {
        return $this->name;
    }

    public function page_content(): void
    {
        include __DIR__ . '/views/action-plan-page.php';

        JavaScriptHelper::enqueueLoginSessionExpirationScript();
    }

    /**
     * Access control hook (load-{$page_hook}); capability is already enforced by the menu registration.
     */
    public function handleAccessControl(): void
    {
    }
}
