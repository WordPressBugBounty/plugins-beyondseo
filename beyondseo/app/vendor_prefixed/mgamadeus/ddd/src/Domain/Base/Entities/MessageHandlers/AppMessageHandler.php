<?php

declare (strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Base\Entities\MessageHandlers;

use BeyondSEODeps\DDD\Infrastructure\Services\AuthService;

abstract class AppMessageHandler
{
    protected function setAuthAccountFromMessage(AppMessage $appMessage): void
    {
        if ($appMessage->accountId ?? null) {
            AuthService::instance()->setAccountId($appMessage->accountId);
        }
    }
}