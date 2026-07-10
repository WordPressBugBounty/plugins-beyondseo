<?php

namespace BeyondSEO\Domain\Base\Repo\RC\Interfaces;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Base\Repo\RC\Utils\RCApiOperation;

interface RCLocationUpdateInterface
{
    /**
     * @return void
     */
    public function updateLocation(): void;

    /**
     * @return array
     */
    public function generateLocationUpdatePayload(): array;

    /**
     * @param mixed $callResponseData
     * @param RCApiOperation|null $apiOperation
     * @return void
     */
    public function handleUpdateResponse(
        mixed &$callResponseData,?RCApiOperation &$apiOperation = null
    ): void;
}
