<?php

namespace BeyondSEODeps\DDD\Domain\Common\Interfaces;


use BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\Account;

interface AccountDependentEntityInterface
{
    public function getAccount(): ?Account;
}