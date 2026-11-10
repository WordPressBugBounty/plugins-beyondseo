<?php
/**
 * Dashboard Widget Template
 * 
 * This template displays the website analysis overview in the WordPress dashboard.
 * 
 * @package RankingCoach
 * @var int    $websiteScore      The website score value
 * @var int    $scoreMin          The minimum score value
 * @var int    $scoreMax          The maximum score value
 * @var int    $pagesCount        The number of pages analyzed
 * @var float  $scorePercentage   The score as a percentage
 * @var string $scoreColor        The color for the score display
 * @var array|null $actionPlan    Action Plan summary (ActionPlanService::getSummary()) or null when unavailable
 * @var string $actionPlanUrl     URL of the Action Plan admin page
 */

if ( !defined('ABSPATH') ) {
    exit;
}

?>

<div class="rankingcoach-dashboard-widget">
    <!-- Website score section -->
    <div class="rankingcoach-score-section">
        <h3><?php echo esc_html__('Website Analysis Overview', 'beyondseo'); ?></h3>

        <!-- Score visualization -->
        <div class="rankingcoach-score-visualization">
            <div class="rankingcoach-score-circle" style="--score-color: <?php echo esc_attr($scoreColor); ?>; --score-percentage: <?php echo esc_attr($scorePercentage); ?>%;">
                <div class="score-circle-inner">
                    <span class="rankingcoach-score-label"><?php echo esc_html__('Current Score', 'beyondseo'); ?></span>
                    <span class="rankingcoach-score-value"><?php echo esc_html( sprintf( '%d', $websiteScore ) ); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Mini cards for stats -->
        <div class="rankingcoach-mini-cards">
            <?php if ( $scoreMin > 0 ) : ?>
                <div class="rankingcoach-mini-card">
                    <span class="mini-card-label"><?php echo esc_html__('Min Score', 'beyondseo'); ?></span>
                    <span class="mini-card-value"><?php echo esc_html($scoreMin); ?></span>
                </div>
            <?php endif; ?>
            <?php if ( $scoreMax > 0 ) : ?>
                <div class="rankingcoach-mini-card">
                    <span class="mini-card-label"><?php echo esc_html__('Max Score', 'beyondseo'); ?></span>
                    <span class="mini-card-value"><?php echo esc_html($scoreMax); ?></span>
                </div>
            <?php endif; ?>
            <div class="rankingcoach-mini-card">
                <span class="mini-card-label"><?php echo esc_html__('Pages', 'beyondseo'); ?></span>
                <span class="mini-card-value"><?php echo esc_html($pagesCount); ?></span>
            </div>
        </div>
    </div>

    <?php if ( $websiteScore === 0 && $pagesCount > 0 ) : ?>
        <!-- Scan section - displayed when no score but pages exist -->
        <div class="rankingcoach-scan-section">
            <p class="rankingcoach-scan-message">
                <?php echo esc_html__('Unlock your website\'s potential with comprehensive SEO analysis', 'beyondseo'); ?>
            </p>
            <button id="rankingcoach-scan-button" class="button button-primary rankingcoach-scan-button">
                <?php echo esc_html__('Analyze Now', 'beyondseo'); ?>
            </button>
        </div>
    <?php endif; ?>
    <?php if ( is_array( $actionPlan ) ) : ?>
        <!-- Action Plan section: open issues from the latest site check, worked through on the Action Plan page -->
        <div class="rankingcoach-action-plan-section">
            <h4 class="rankingcoach-action-plan-title"><?php echo esc_html__('Your SEO Action Plan', 'beyondseo'); ?></h4>
            <?php if ( ! $actionPlan['checked'] ) : ?>
                <p class="rankingcoach-action-plan-text">
                    <?php echo esc_html__('Your pages have not been checked yet. Run the site check to see which pages need work.', 'beyondseo'); ?>
                </p>
            <?php elseif ( $actionPlan['issueCount'] > 0 ) : ?>
                <p class="rankingcoach-action-plan-text">
                    <strong>
                        <?php
                        echo esc_html( sprintf(
                            /* translators: %d: number of open page issues */
                            _n( '%d page issue to review', '%d page issues to review', $actionPlan['issueCount'], 'beyondseo' ),
                            $actionPlan['issueCount']
                        ) );
                        ?>
                    </strong>
                    <?php if ( ! empty( $actionPlan['next'] ) ) : ?>
                        <br>
                        <?php
                        echo esc_html( sprintf(
                            /* translators: 1: suggestion title, 2: page title */
                            __( 'Next: %1$s · %2$s', 'beyondseo' ),
                            $actionPlan['next']['title'],
                            $actionPlan['next']['pageTitle']
                        ) );
                        ?>
                    <?php endif; ?>
                </p>
            <?php else : ?>
                <p class="rankingcoach-action-plan-text">
                    <?php echo esc_html__('No open issues on your checked pages.', 'beyondseo'); ?>
                </p>
            <?php endif; ?>
            <a class="button button-primary rankingcoach-action-plan-button" href="<?php echo esc_url( $actionPlanUrl ); ?>">
                <?php echo esc_html__('View Action Plan', 'beyondseo'); ?> &rarr;
            </a>
        </div>
    <?php endif; ?>
    <?php if ($reviewGreen) : ?>
        <div id="dashboard-review-rankingcoach-react" data-review-score="<?php echo esc_attr($websiteScore); ?>"></div>
    <?php endif; ?>
</div>
