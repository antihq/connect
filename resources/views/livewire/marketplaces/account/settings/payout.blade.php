<?php

use App\Models\Marketplace;
use App\Models\MarketplacePayoutSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public string $accountType = '';

    public string $country = '';

    public ?string $onboarding_status = null;

    public function mount()
    {
        $setting = MarketplacePayoutSetting::where('user_id', Auth::id())
            ->where('marketplace_id', $this->marketplace->id)
            ->first();
        if ($setting) {
            $this->accountType = $setting->account_type;
            $this->country = $setting->country;
            $this->onboarding_status = $setting->onboarding_status;
        }
    }

    public function save()
    {
        // Prevent changing accountType or country if already set
        $setting = MarketplacePayoutSetting::where('user_id', Auth::id())
            ->where('marketplace_id', $this->marketplace->id)
            ->first();
        if ($setting && ($setting->account_type || $setting->country)) {
            // Optionally, add a flash message or error here
            return;
        }

        $this->validate([
            'accountType' => ['required', 'in:individual,company'],
            'country' => ['required', 'in:AU,AT,BE,BR,BG,CA,HR,CY,CZ,DK,EE,FI,FR,DE,GI,GR,HK,HU,IN,IE,IT,JP,LV,LI,LT,LU,MY,MT,MX,NL,NZ,NO,PL,PT,RO,SG,SK,SI,ES,SE,CH,TH,AE,GB,US'], // Stripe supported countries
        ]);

        $user = Auth::user();
        // Only create Stripe account if not already set
        if (! $setting || ! $setting->stripe_account_id) {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $stripeAccount = \Stripe\Account::create([
                'type' => 'express',
                'country' => $this->country,
                'email' => $user->email,
                'business_type' => $this->accountType,
            ]);
            MarketplacePayoutSetting::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'marketplace_id' => $this->marketplace->id,
                ],
                [
                    'account_type' => $this->accountType,
                    'country' => $this->country,
                    'stripe_account_id' => $stripeAccount->id,
                ]
            );
        }
    }

    public function startOnboarding()
    {
        $setting = MarketplacePayoutSetting::where('user_id', Auth::id())
            ->where('marketplace_id', $this->marketplace->id)
            ->first();
        if (! $setting) {
            $this->addError('payout_settings', 'required');
            return;
        }
        if (! $setting->stripe_account_id) {
            $this->addError('payout_settings', 'required');
            return;
        }
        \Stripe\Stripe::setApiKey(config('cashier.secret'));
        $accountLink = \Stripe\AccountLink::create([
            'account' => $setting->stripe_account_id,
            'refresh_url' => url()->current(),
            'return_url' => url()->current(),
            'type' => 'account_onboarding',
        ]);
        $setting->onboarding_status = 'in_progress';
        $setting->save();
        $this->onboarding_status = 'in_progress';
        return redirect($accountLink->url);
    }

    public function completeOnboarding()
    {
        $setting = MarketplacePayoutSetting::where('user_id', Auth::id())
            ->where('marketplace_id', $this->marketplace->id)
            ->first();
        if ($setting) {
            $setting->onboarding_status = 'completed';
            $setting->save();
            $this->onboarding_status = 'completed';
        }
    }
}
?>

<div class="mx-auto max-w-2xl">
    <flux:heading level="1" size="xl">Payout Settings</flux:heading>
    <flux:spacer class="my-6" />

    @if ($onboarding_status === 'completed')
        <div class="mb-4 rounded bg-green-100 p-3 text-green-800">Onboarding completed</div>
    @endif

    <form class="space-y-6" wire:submit="save">
        <flux:field>
            <flux:label badge="Required">Account Type</flux:label>
            <flux:select wire:model="accountType" :disabled="$accountType !== ''">
                <flux:select.option value="">Select account type</flux:select.option>
                <flux:select.option value="individual">Individual</flux:select.option>
                <flux:select.option value="company">Company</flux:select.option>
            </flux:select>
            @if ($accountType !== '')
                <div class="mt-1 text-xs text-zinc-500">Account type cannot be changed after it is set.</div>
            @endif

            <flux:error name="accountType" />
        </flux:field>

        <flux:field>
            <flux:label badge="Required">Country</flux:label>
            <flux:select wire:model="country" :disabled="$country !== ''">
                <flux:select.option value="">Select country</flux:select.option>
                <flux:select.option value="AU">Australia</flux:select.option>
                <flux:select.option value="AT">Austria</flux:select.option>
                <flux:select.option value="BE">Belgium</flux:select.option>
                <flux:select.option value="BR">Brazil</flux:select.option>
                <flux:select.option value="BG">Bulgaria</flux:select.option>
                <flux:select.option value="CA">Canada</flux:select.option>
                <flux:select.option value="HR">Croatia</flux:select.option>
                <flux:select.option value="CY">Cyprus</flux:select.option>
                <flux:select.option value="CZ">Czech Republic</flux:select.option>
                <flux:select.option value="DK">Denmark</flux:select.option>
                <flux:select.option value="EE">Estonia</flux:select.option>
                <flux:select.option value="FI">Finland</flux:select.option>
                <flux:select.option value="FR">France</flux:select.option>
                <flux:select.option value="DE">Germany</flux:select.option>
                <flux:select.option value="GI">Gibraltar</flux:select.option>
                <flux:select.option value="GR">Greece</flux:select.option>
                <flux:select.option value="HK">Hong Kong</flux:select.option>
                <flux:select.option value="HU">Hungary</flux:select.option>
                <flux:select.option value="IN">India</flux:select.option>
                <flux:select.option value="IE">Ireland</flux:select.option>
                <flux:select.option value="IT">Italy</flux:select.option>
                <flux:select.option value="JP">Japan</flux:select.option>
                <flux:select.option value="LV">Latvia</flux:select.option>
                <flux:select.option value="LI">Liechtenstein</flux:select.option>
                <flux:select.option value="LT">Lithuania</flux:select.option>
                <flux:select.option value="LU">Luxembourg</flux:select.option>
                <flux:select.option value="MY">Malaysia</flux:select.option>
                <flux:select.option value="MT">Malta</flux:select.option>
                <flux:select.option value="MX">Mexico</flux:select.option>
                <flux:select.option value="NL">Netherlands</flux:select.option>
                <flux:select.option value="NZ">New Zealand</flux:select.option>
                <flux:select.option value="NO">Norway</flux:select.option>
                <flux:select.option value="PL">Poland</flux:select.option>
                <flux:select.option value="PT">Portugal</flux:select.option>
                <flux:select.option value="RO">Romania</flux:select.option>
                <flux:select.option value="SG">Singapore</flux:select.option>
                <flux:select.option value="SK">Slovakia</flux:select.option>
                <flux:select.option value="SI">Slovenia</flux:select.option>
                <flux:select.option value="ES">Spain</flux:select.option>
                <flux:select.option value="SE">Sweden</flux:select.option>
                <flux:select.option value="CH">Switzerland</flux:select.option>
                <flux:select.option value="TH">Thailand</flux:select.option>
                <flux:select.option value="AE">United Arab Emirates</flux:select.option>
                <flux:select.option value="GB">United Kingdom</flux:select.option>
                <flux:select.option value="US">United States</flux:select.option>
            </flux:select>
            @if ($country !== '')
                <div class="mt-1 text-xs text-zinc-500">Country cannot be changed after it is set.</div>
            @endif

            <flux:error name="country" />
        </flux:field>

        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
