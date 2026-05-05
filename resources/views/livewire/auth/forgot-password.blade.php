<?php

use Livewire\Component;
use Illuminate\Support\Facades\Password;

new class extends Component {

    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="lg:min-h-screen flex items-center justify-center">
    <x-card class="flex items-center justify-center p-5 lg:px-10 lg:py-5" title="{{__('Password renewal')}}" subtitle="{{__('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.')}}" shadow separator>
        <x-session-status class="mb-4" :status="session('status')" />
        <x-form wire:submit="sendPasswordResetLink">
            <x-input label="{{__('E-mail')}}" placeholder="{{__('E-mail')}}" wire:model="email" icon="o-envelope" inline />
            
            <x-slot:actions>
               <x-button label="{{ __('Email Password Reset Link') }}" type="submit" icon="o-paper-airplane" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>