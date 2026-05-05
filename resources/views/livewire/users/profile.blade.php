<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule as ValidationRule;
use Mary\Traits\Toast;

new
#[Title('Profile')]
class extends Component {
    use Toast;

    #[Rule('nullable|string|max:255')]
    public ?string $name = null;

    #[Rule('nullable|string|max:255')]
    public ?string $first_name = null;

    #[Rule('required|email|unique:users')]
    public string $email = '';

    #[Rule('nullable|string|min:8|confirmed')]
    public ?string $password = null;

    #[Rule('nullable|string')]
    public ?string $password_confirmation = null;

    #[Rule('required|string')]
    public string $current_password = '';

    public bool $emailVerified = false;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->first_name = $user->first_name ?? '';
        $this->email = $user->email;

        $this->emailVerified = $user->hasVerifiedEmail();
    }

    public function getCompletionProperty()
    {
        return collect([
            $this->name,
            $this->first_name,
            $this->email,
            $this->emailVerified
        ])->filter()->count() / 4 * 100;
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', ValidationRule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required', 'string'],
        ]);

        // Check current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('The current password is incorrect.'));
            return;
        }

        $originalEmail = $user->email;

        $user->fill([
            'name' => $this->name,
            'first_name' => $this->first_name,
            'email' => $this->email,
        ]);

        $shouldRefresh = $user->isDirty(['name', 'first_name', 'email']);
        $emailChanged = $originalEmail !== $this->email;

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // If email changed, mark as unverified
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            $this->success(__('Profile updated. Please verify your new email.'));
        } else {
            $this->success(__('Profile updated successfully.'));
        }

        // Sync Livewire with DB
        $user = $user->fresh();
        $this->emailVerified = ! $emailChanged && $user->hasVerifiedEmail();

        // Refresh navigation if needed
        if ($shouldRefresh) {
            $this->dispatch('refreshNavigation');
        }

        $this->reset(['password', 'password_confirmation', 'current_password']);
    }

    public function render()
    {
        return view('livewire.users.profile');
    }
}; ?>

<div class="min-h-screen bg-base-200 py-10">
    <div class="max-w-3xl mx-auto">

        {{-- HEADER + PROGRESS --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold">{{ __('Your profile') }}</h1>

            <div class="mt-3">
                <div class="flex justify-between text-sm mb-1">
                    <span>{{ __('Profile completion') }}</span>
                    <span>{{ round($this->completion) }}%</span>
                </div>

                <progress class="progress progress-primary w-full" value="{{ $this->completion }}" max="100"></progress>
            </div>
        </div>

        {{-- INCENTIVE MESSAGE --}}
        @if ($this->completion < 100)
            <div class="alert alert-info mb-6">
                {{ __('Complete your profile to improve your experience!') }}
            </div>
        @endif

        <x-form wire:submit="updateProfile">

            {{-- PERSONAL INFORMATION --}}
            <x-card class="mb-6" shadow separator>
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <x-icon name="o-user" class="w-5 h-5" />
                        <span>{{ __('Personal information') }}</span>
                    </div>
                </x-slot:title>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <x-input label="{{__('First name')}}" wire:model="first_name" placeholder="{{ __('First name') }}" icon="o-user" inline/>
                    <x-input label="{{__('Name')}}" wire:model="name" placeholder="{{ __('Name') }}" icon="o-user" inline/>
                </div>

                <x-input label="{{__('E-mail')}}" wire:model="email" placeholder="{{ __('E-mail') }}" icon="o-envelope" inline/>
                @if (! $emailVerified)
                    <div class="alert alert-warning mt-4">
                        {{ __('Your email is not verified. Please check your email box or get a new verification link.') }}
                        
                        <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-link">
                            {{ __('Resend verification email') }}
                        </a>
                    </div>
                @endif
            </x-card>

            {{-- SECURITY --}}
            <x-card class="mb-6" shadow separator>
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <x-icon name="o-shield-check" class="w-5 h-5" />
                        <span>{{ __('Security') }}</span>
                    </div>
                </x-slot:title>

                <x-input label="{{__('Current password')}}"
                        placeholder="{{ __('Current password') }}" 
                        type="password" 
                        wire:model="current_password" 
                        icon="o-key" inline/>

                <div class="grid md:grid-cols-2 gap-4 mt-4">
                    <x-input label="{{__('New password')}}" 
                            placeholder="{{ __('New password') }}" 
                            type="password" 
                            wire:model="password" 
                            icon="o-lock-closed" inline/>

                    <x-input label="{{__('Confirm password')}}"
                            placeholder="{{ __('Confirm password') }}"
                            type="password" 
                            wire:model="password_confirmation" 
                            icon="o-lock-closed" inline/>
                </div>
            </x-card>

            {{-- ACTION --}}
            <div class="flex justify-end">
                <x-button label="{{__('Save changes')}}" 
                          type="submit" 
                          icon="o-check" 
                          class="btn-primary px-6" />
            </div>

        </x-form>
    </div>
</div>