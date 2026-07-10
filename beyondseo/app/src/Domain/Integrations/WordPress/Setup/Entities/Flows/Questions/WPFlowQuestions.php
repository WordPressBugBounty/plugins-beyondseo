<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Questions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Questions\InternalDBWPFlowQuestions;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Services\WPFlowQuestionsService;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * Class WPFlowQuestions
 * @method WPFlowQuestion[] getElements()
 * @method WPFlowQuestion|null first()
 * @method WPFlowQuestion|null getByUniqueKey(string $uniqueKey)
 * @property WPFlowQuestion[] $elements
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPFlowQuestions::class)]
class WPFlowQuestions  extends EntitySet
{
    public const ENTITY_CLASS = WPFlowQuestion::class;
    public const SERVICE_NAME = WPFlowQuestionsService::class;
}