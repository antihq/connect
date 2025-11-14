<?php

namespace App\Services;

class StripeConnectService
{
    public function createAccount(array $params)
    {
        \Stripe\Stripe::setApiKey(config('cashier.secret'));
        return \Stripe\Account::create($params);
    }

    public function createAccountLink(string $accountId, string $refreshUrl, string $returnUrl)
    {
        \Stripe\Stripe::setApiKey(config('cashier.secret'));
        return \Stripe\AccountLink::create([
            'account' => $accountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);
    }
}
