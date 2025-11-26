<?php

use App\Models\MagicAuthCode;
use App\Models\Marketplace;
use App\Models\User;
use App\Notifications\MagicAuthCodeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public string $email = '';

    public string $code = '';

    public string $step = 'email';

    public function sendMagicCode()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $this->ensureIsNotRateLimited();

        $code = Str::padLeft((string) random_int(0, 999999), 6, '0');

        $expiresAt = now()->addMinutes(10);

        MagicAuthCode::updateOrCreate(
            ['email' => $this->email],
            [
                'code' => $code,
                'expires_at' => $expiresAt,
            ]
        );

        $user = User::firstOrNew(['email' => $this->email]);

        $user->notify(new MagicAuthCodeNotification($code));

        $this->step = 'code';
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        RateLimiter::hit($this->throttleKey(), 60);
    }

    protected function throttleKey(): string
    {
        return strtolower($this->email).'|'.request()->ip();
    }

    public function verifyCode()
    {
        $this->validate([
            'code' => 'required|digits:6',
        ]);

        $authCode = MagicAuthCode::where('email', $this->email)
            ->where('code', $this->code)
            ->where('expires_at', '>', now())
            ->first();

        if (! $authCode) {
            $this->addError('code', 'Invalid or expired code');

            $this->step = 'code';

            return;
        }

        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => Str::before($this->email, '@') ?: $this->email,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        Auth::login($user, true);

        if (! $this->marketplace->isMember($user)) {
            $this->marketplace->addMember($user);
        }

        $authCode->delete();

        $this->redirectIntended(route('marketplaces.show', $this->marketplace, absolute: false), navigate: true);
    }
}; ?>

<div class="flex h-full flex-col items-center justify-center">
    @if ($step === 'email')
        <div wire:key="email" class="w-full max-w-xs">
            <x-logo />
            <flux:heading level="1" size="xl" class="mt-4">Sign in to {{ $marketplace->name }}</flux:heading>
            <form wire:submit="sendMagicCode" class="mt-8 space-y-6">
                <flux:input type="email" wire:model="email" label="Email" placeholder="Email" required />
                <flux:button type="submit" variant="primary" class="w-full">Send Magic Code</flux:button>
            </form>
        </div>
    @elseif ($step === 'code')
        <div wire:key="code" class="w-full max-w-xs">
            <x-logo />
            <flux:heading level="1" size="xl" class="mt-4">Check your email</flux:heading>
            <form wire:submit="verifyCode" class="mt-8 space-y-6">
                <flux:otp wire:model="code" :label="'Enter the code sent to ' . $this->email" length="6" submit="auto" />
            </form>
        </div>
    @endif
</div>
