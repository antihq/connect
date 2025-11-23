<?php

use App\Models\Marketplace;
use App\Models\PayoutSetting;
use App\Models\User;
use Facades\App\Services\StripeConnectService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public string $accountType = '';

    public string $country = '';

    public ?string $onboarding_status = null;

    public ?PayoutSetting $setting = null;

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    public function mount()
    {
        $this->setting = $this->user()->payoutSetting($this->marketplace);

        if (! $this->setting) {
            return;
        }

        $this->accountType = $this->setting->account_type;
        $this->country = $this->setting->country;

        if (! $this->setting->stripe_account_id) {
            $this->onboarding_status = $this->setting->onboarding_status;

            return;
        }

        $stripeAccount = StripeConnectService::getAccount($this->setting->stripe_account_id);

        $latestStatus = ($stripeAccount->charges_enabled && $stripeAccount->details_submitted)
            ? 'completed'
            : 'in_progress';

        $this->onboarding_status = $latestStatus;

        if ($this->setting->onboarding_status !== $latestStatus) {
            $this->setting->update(['onboarding_status' => $latestStatus]);
        }
    }

    public function save()
    {
        abort_if($this->setting, 400);

        $this->validate([
            'accountType' => ['required', 'in:individual,company'],
            'country' => ['required', 'in:AU,AT,BE,BR,BG,CA,HR,CY,CZ,DK,EE,FI,FR,DE,GI,GR,HK,HU,IN,IE,IT,JP,LV,LI,LT,LU,MY,MT,MX,NL,NZ,NO,PL,PT,RO,SG,SK,SI,ES,SE,CH,TH,AE,GB,US'], // Stripe supported countries
        ]);

        $stripeAccount = StripeConnectService::createAccount([
            'type' => 'express',
            'country' => $this->country,
            'email' => $this->user->email,
            'business_type' => $this->accountType,
        ]);

        $setting = PayoutSetting::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'marketplace_id' => $this->marketplace->id,
            ],
            [
                'account_type' => $this->accountType,
                'country' => $this->country,
                'stripe_account_id' => $stripeAccount->id,
            ]
        );

        $this->setting = $setting;
    }

    public function startOnboarding()
    {
        abort_unless($this->setting?->stripe_account_id, 400);

        $payoutUrl = route('on-marketplace.account.settings.payout', ['marketplace' => $this->marketplace]);

        $accountLink = StripeConnectService::createAccountLink(
            $this->setting->stripe_account_id,
            $payoutUrl,
            $payoutUrl
        );

        $this->setting->update([
            'onboarding_status' => 'in_progress',
        ]);

        $this->onboarding_status = 'in_progress';

        return redirect()->away($accountLink->url);
    }

    public function redirectToStripeDashboard()
    {
        abort_unless($this->setting?->stripe_account_id, 400);

        $url = StripeConnectService::createExpressDashboardLink($this->setting->stripe_account_id);

        return redirect()->away($url);
    }
}
?>

<div class="mx-auto max-w-2xl">
    <flux:heading level="1" size="xl">Payout Settings</flux:heading>
    <flux:spacer class="my-6" />

    @if (! $this->setting)
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
            <flux:callout.heading>Set up payout settings to receive payments</flux:callout.heading>
            <flux:callout.text>You must set up your payout settings before you can receive payments.</flux:callout.text>
        </flux:callout>
    @else
        @if ($onboarding_status === 'completed')
            <flux:callout variant="secondary" icon="check-circle" class="mb-6">
                <flux:callout.heading>Stripe onboarding completed</flux:callout.heading>
                <flux:callout.text>
                    Your payout account is ready. You can manage your payout details in Stripe.
                </flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="primary" wire:click="redirectToStripeDashboard">
                        Go to Stripe Dashboard
                    </flux:button>
                </x-slot>
            </flux:callout>
        @elseif ($onboarding_status === 'in_progress')
            <flux:callout variant="secondary" icon="clock" class="mb-6">
                <flux:callout.heading>Stripe onboarding in progress</flux:callout.heading>
                <flux:callout.text>
                    Your payout account setup is not yet complete. Please finish onboarding to receive payouts.
                </flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="primary" wire:click="startOnboarding">Continue Stripe Onboarding</flux:button>
                </x-slot>
            </flux:callout>
        @else
            <flux:callout variant="secondary" icon="information-circle" class="mb-6">
                <flux:callout.heading>Stripe onboarding not started</flux:callout.heading>
                <flux:callout.text>To receive payouts, you need to complete Stripe onboarding.</flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="primary" wire:click="startOnboarding">Start Stripe Onboarding</flux:button>
                </x-slot>
            </flux:callout>
        @endif
    @endif

    @if ($this->setting)
        <div class="space-y-6">
            <flux:input
                label="Account Type"
                readonly
                variant="filled"
                :value="$accountType === 'individual' ? 'Individual' : 'Company'"
            />

            <flux:input
                label="Country"
                readonly
                variant="filled"
                :value="
                match($country) {
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'BE' => 'Belgium',
                    'BR' => 'Brazil',
                    'BG' => 'Bulgaria',
                    'CA' => 'Canada',
                    'HR' => 'Croatia',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'EE' => 'Estonia',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'DE' => 'Germany',
                    'GI' => 'Gibraltar',
                    'GR' => 'Greece',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IN' => 'India',
                    'IE' => 'Ireland',
                    'IT' => 'Italy',
                    'JP' => 'Japan',
                    'LV' => 'Latvia',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MY' => 'Malaysia',
                    'MT' => 'Malta',
                    'MX' => 'Mexico',
                    'NL' => 'Netherlands',
                    'NZ' => 'New Zealand',
                    'NO' => 'Norway',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'RO' => 'Romania',
                    'SG' => 'Singapore',
                    'SK' => 'Slovakia',
                    'SI' => 'Slovenia',
                    'ES' => 'Spain',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'TH' => 'Thailand',
                    'AE' => 'United Arab Emirates',
                    'GB' => 'United Kingdom',
                    'US' => 'United States',
                    default => $country
                }"
            />
        </div>
    @else
        <form class="space-y-6" wire:submit="save">
            <flux:field>
                <flux:label badge="Required">Account Type</flux:label>
                <flux:select wire:model="accountType">
                    <flux:select.option value="">Select account type</flux:select.option>
                    <flux:select.option value="individual">Individual</flux:select.option>
                    <flux:select.option value="company">Company</flux:select.option>
                </flux:select>
                <flux:error name="accountType" />
            </flux:field>

            <flux:field>
                <flux:label badge="Required">Country</flux:label>
                <flux:select wire:model="country">
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
                <flux:error name="country" />
            </flux:field>

            <flux:button type="submit" variant="primary">Save</flux:button>
        </form>
    @endunless
</div>
