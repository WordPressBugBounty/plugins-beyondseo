<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\Data;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\WPFlowCollector;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\WPFlowRequirements;

/**
 * Class RankingCoachDataCollector
 */
class RankingCoachDataCollector extends WPFlowCollector
{
    public string $collector = WPFlowRequirements::SETUP_COLLECTOR_RANKINGCOACH;

}