<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Base\Repo\RC;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Base\Repo\RC\Traits\RCTrait;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;

class RCEntity extends Entity
{
    use RCTrait;
}
