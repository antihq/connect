<?php

use App\Models\Marketplace;
use Livewire\Volt\Component;

use App\Models\MarketplacePayoutSetting;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public Marketplace $marketplace;
    public string $accountType = '';
    public string $country = '';

    public function mount()
    {
        $setting = MarketplacePayoutSetting::where('user_id', Auth::id())
            ->where('marketplace_id', $this->marketplace->id)
            ->first();
        if ($setting) {
            $this->accountType = $setting->account_type;
            $this->country = $setting->country;
        }
    }

    public function save()
    {
        $this->validate([
            'accountType' => ['required', 'in:individual,company'],
            'country' => ['required', 'in:AU,AT,BE,BR,BG,CA,HR,CY,CZ,DK,EE,FI,FR,DE,GI,GR,HK,HU,IN,IE,IT,JP,LV,LI,LT,LU,MY,MT,MX,NL,NZ,NO,PL,PT,RO,SG,SK,SI,ES,SE,CH,TH,AE,GB,US'], // Stripe supported countries
        ]);

        MarketplacePayoutSetting::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'marketplace_id' => $this->marketplace->id,
            ],
            [
                'account_type' => $this->accountType,
                'country' => $this->country,
            ]
        );
    }
}; ?>

<div>
    //
</div>
