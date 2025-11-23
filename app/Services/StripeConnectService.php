<?php

namespace App\Services;

use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class StripeConnectService
{
    public function __construct()
    {
        Stripe::setApiKey(config('cashier.secret'));
    }

    public function createAccount(array $params)
    {
        return Account::create($params);
    }

    public function createAccountLink(string $accountId, string $refreshUrl, string $returnUrl)
    {
        return AccountLink::create([
            'account' => $accountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);
    }

    public function getAccount(string $accountId)
    {
        return Account::retrieve($accountId);
    }

    public function createExpressDashboardLink(string $accountId)
    {
        return Account::createLoginLink($accountId)->url;
    }
}
