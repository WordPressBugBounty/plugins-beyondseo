<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Keywords\Keywords;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPKeyword;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPKeywords;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages\InternalDBWPKeywords;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Services\EntitiesService;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class WPKeywordsService
 */
class WPKeywordsService extends EntitiesService
{
    /** @var string DEFAULT_ENTITY_CLASS The default entity class. */
    public const DEFAULT_ENTITY_CLASS = WPKeyword::class;

    /**
     * Gets all keywords.
     *
     * @return WPKeywords|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function getAllKeywords(): ?WPKeywords
    {
        $repo = new InternalDBWPKeywords();
        return $repo->getAllKeywords();
    }

    /**
     * @param WPKeywords $newKeywords
     * @return EntitySet
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function addOnboardingKeywords(Keywords $newKeywords): EntitySet
    {
        $repoItems = new InternalDBWPKeywords();
        $currentOnboardingKeywords = $repoItems->getAllKeywords() ?? new WPKeywords();
        $keywords = WPKeywords::addOnboardingKeywords($currentOnboardingKeywords, $newKeywords);
        return $repoItems->update($keywords);
    }
}