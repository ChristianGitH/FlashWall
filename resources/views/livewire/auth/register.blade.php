<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
 
new
#[Title('Register')]
class extends Component {
 
 
    #[Rule('required|string|max:50|in:FlashWall2025!')]
    public string $code = '';

    #[Rule('required|string|max:255|unique:users')]
    public string $name = '';
 
    #[Rule('required|email|unique:users')]
    public string $email = '';
 
    #[Rule('required|confirmed')]
    public string $password = '';
 
    #[Rule('required')]
    public string $password_confirmation = '';
 
    public function register()
    {
        $data = $this->validate();

        if ($this->code !== 'FlashWall2025!') {
            $this->addError('code', 'Invalid invitation code.');
            return;
        }
 
        $data['password'] = Hash::make($data['password']);
 
        $user = User::create($data);
 
        auth()->login($user);
 
        request()->session()->regenerate();
 
        return redirect('/');
    }
}; ?>

<div class="lg:min-h-screen flex items-center justify-center">
    <x-card class="flex items-center justify-center p-5 lg:px-10 lg:py-5" title="{{__('Register')}}" shadow separator>
 
        <x-form wire:submit="register">
            <x-input label="{{__('Invitation code')}}" placeholder="{{__('Invitation code')}}" wire:model="code" inline />

            <x-input label="{{__('Name')}}" placeholder="{{__('Name')}}" wire:model="name" icon="o-user" inline />
            <x-input label="{{__('E-mail')}}" placeholder="{{__('E-mail')}}" wire:model="email" icon="o-envelope" inline />
            <x-input label="{{__('Password')}}" placeholder="{{__('Password')}}" wire:model="password" type="password" icon="o-key" inline />
            <x-input label="{{__('Confirm Password')}}" placeholder="{{__('Confirm Password')}}" wire:model="password_confirmation" type="password" icon="o-key" inline />
    
            <x-slot:actions class="flex-wrap">
                <x-button label="{{__('Already registered?')}}" class="btn-ghost" link="/login" />
                <x-button label="{{__('Register')}}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>