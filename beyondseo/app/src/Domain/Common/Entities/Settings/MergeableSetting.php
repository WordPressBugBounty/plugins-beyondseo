<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Common\Entities\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class MergeableSetting extends Setting
{
    /**
     * @param MergeableSetting $otherSetting
     * @return void
     */
    abstract public function mergeFromOtherSetting(MergeableSetting &$otherSetting): void;
}