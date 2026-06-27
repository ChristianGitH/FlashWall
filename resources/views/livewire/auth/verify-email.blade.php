<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('Email Verification')]
class extends Component {

    public function mount()
    {
        $user = Auth::user();

        // Redirect to home if email is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }
    }

    public function resend()
    {
        $this->dispatch('resent', true);
        auth()->user()->sendEmailVerificationNotification();
        session()->flash('resent', true);
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}; ?>

@if (session('resent'))
    <div class="alert alert-success">
        <p>{{__('A fresh verification link has been sent to your email address')}}.</p>
    </div>
@endif

<div class="lg:min-h-[80vh] flex items-center justify-center">
    <x-card class="flex items-center justify-center p-5 lg:px-10 lg:py-5" title="{{__('Email Verification')}}" shadow separator>
        <div class="text-center">
            <p>{{__('Before proceeding, please check your email box for a verification link')}}.</p>
            <p>{{__('If you did not receive the email, we will gladly send you another')}}.</p>
        </div>

        <x-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="resend" class="mt-4 text-right">
            <x-button label="{{ __('Resend Verification Email') }}" type="submit" class="btn-primary" />
        </form>
    </x-card>
</div>