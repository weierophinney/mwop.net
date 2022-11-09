<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use function array_key_exists;

class AccountMap
{
    private array $accounts = [
        'matthew@mwop.net' => Matthew::class,
    ];

    public function match(string $account): AccountResult
    {
        if (! array_key_exists($account, $this->accounts)) {
            return new AccountNotFoundResult($account);
        }

        return new DiscoveredAccountResult(new ($this->accounts[$account])());
    }
}
