<?php

declare (strict_types=1);

namespace BeyondSEO\Domain\Common\Entities\Texts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;
use BeyondSEODeps\DDD\Infrastructure\Validation\Constraints\Choice;

/**
 * @property Text[] $elements;
 * @method Text getByUniqueKey(string $uniqueKey)
 * @method Text[] getElements()
 * @method Text first()
 */
class Texts extends ObjectSet
{
    /** @var string Writing style of the content (personal or formal) */
    #[Choice([Text::WRITING_STYLE_INFORMAL, Text::WRITING_STYLE_FORMAL])]
    public string $defaultWritingStyle = Text::WRITING_STYLE_INFORMAL;
}