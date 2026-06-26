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

    public int $wallsCount = 0;

    public $selectedTab = 'profile-tab';
    public $current_plan;
    public $headers = [];
    public $plans_table = [];
    public int $currentPlanLevel = 0;
    public string $current_plan_settings_level = "Basic";

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->first_name = $user->first_name ?? '';
        $this->email = $user->email;

        $this->emailVerified = $user->hasVerifiedEmail();

        // Get user number of walls
        $this->wallsCount = $user->walls()->count();

        // Get user's current plan
        $this->currentPlanLevel = (int) $user->plan_level;
        $this->current_plan = $user->currentPlan();

        // Get the setting level
        if ($this->current_plan['features']['advanced_settings']) {
            $this->current_plan_settings_level = 'Advanced';
        }

        // Format live usage duration
        if ($this->current_plan['features']['live_usage_duration'] == 0 ){
            $this->current_plan['features']['live_usage_duration'].= ' ' . __('hour');
        }
        else{
            $this->current_plan['features']['live_usage_duration'].= ' ' . __('hours');
        }

        $plans = config('plans.plans');

        $planNames = collect($plans)->map(fn ($p) => $p['name'])->toArray();

        $this->headers = array_merge(
            [['key' => 'feature', 'label' => 'Feature']],
            collect($plans)->map(fn ($plan, $level) => [
                'key' => 'plan_' . $level,
                'label' => $level === $this->currentPlanLevel
                    ? $plan['name'] . '<br><span class="badge badge-success badge-sm mt-1">Current</span>'
                    : $plan['name'],
                'class' => $level === $this->currentPlanLevel
                    ? 'bg-primary/10 font-semibold'
                    : '',
            ])->toArray()
        );


        $features = [
            'walls' => __('Number of walls'),
            'images_per_wall' => __('Images per wall'),
            'advanced_settings' => __('Advanced settings'),
            'advanced_moderation' => __('Advanced moderation'),
            'live_usage_duration' => __('Live usage (hours)'),
        ];

        $this->plans_table = collect($features)->map(function ($label, $featureKey) use ($plans) {

            $row = [
                'feature' => $label,
            ];

            foreach ($plans as $level => $plan) {
                $value = $plan['features'][$featureKey];

                // format booleans nicely
                if (is_bool($value)) {
                    $value = $value ? '✓' : '—';
                }

                $row['plan_' . $level] = $value;
            }

            return $row;
        })->values()->toArray();
    }

    public function getCellDecorationProperty(): array
    {
        $plans = config('plans.plans');

        return collect($plans)->mapWithKeys(fn ($plan, $level) => [
            'plan_' . $level => [
                'bg-primary/10 ring-1 ring-primary/30 font-semibold' => fn ($row) => $level === $this->currentPlanLevel,
            ],
        ])->toArray();
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

<div class="min-h-screen">
    <div class="max-w-3xl mx-auto">

    <x-tabs wire:model="selectedTab" label-class="text-3xl font-bold font-[Figtree]">
        <!-- PROFILE TAB -->
        <x-tab name="profile-tab" label="{{ __('Your profile') }}">
            {{-- HEADER + PROGRESS --}}
            <div class="mb-6">
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

                    <div class="mt-2 text-right">
                        <x-button label="{{__('Forgot your password?')}}" class="btn-ghost" link="/forgot-password" />
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
        </x-tab>

        <!-- PLAN TAB -->
        <x-tab name="plan-tab" class="flex flex-col items-center gap-6">
            <x-slot:label>  
                <span>{{ __('Your current plan') }} : </span>
                <span class="font-normal">{{ __($current_plan['name']) }}</span>
            </x-slot:label>

            <x-card shadow separator class="w-fit mx-auto" title="{{ __('Usage and limits') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-stat
                        title="{{ __('Number of walls') }}"
                        value="{{ $this->wallsCount }} / {{ $current_plan['features']['walls'] }}"
                        icon="o-sparkles"
                        color="text-primary"
                        class="font-bold text-xl"/>

                    <x-stat
                        title="{{ __('Images per wall') }}"
                        value="{{ $current_plan['features']['images_per_wall'] }}"
                        icon="o-photo"
                        color="text-primary"
                        class="font-bold text-xl"/>

                    <x-stat
                        title="{{ __('Live usage available') }}"
                        value="{{ $current_plan['features']['live_usage_duration'] }}"
                        icon="o-clock"
                        color="text-primary"
                        class="font-bold text-xl"/>
                </div>
            </x-card>

            <x-card shadow separator class="mx-auto max-w-full h-full" title="{{ __('Plans') }}">
                    <x-slot:menu>
                        <x-button label="{{ __('View plans details') }}" class="btn-sm" />
                    </x-slot:menu>

                <div class="overflow-x-auto">
                    <table class="mt-4 table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="font-bold text-black">{{ __('Feature') }}</th>

                                @foreach(config('plans.plans') as $level => $plan)
                                    <th
                                        class="text-center text-black {{ $level === $currentPlanLevel ? 'bg-success/10' : '' }}"
                                    >
                                        <div class="flex flex-col items-center">
                                            @if($level === $currentPlanLevel)
                                                <span class="badge badge-success badge-sm absolute -top-3">
                                                    {{ __('Current') }}
                                                </span>
                                            @endif

                                            <span class="font-bold">
                                                {{ $plan['name'] }}
                                            </span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($plans_table as $row)
                                <tr>
                                    <td class="font-medium">
                                        {{ $row['feature'] }}
                                    </td>

                                    @foreach(config('plans.plans') as $level => $plan)
                                        <td
                                            class="
                                                text-center
                                                {{ $level === $currentPlanLevel
                                                    ? 'bg-success/10 font-semibold'
                                                    : '' }}
                                            "
                                        >
                                            @php
                                                $value = $row['plan_'.$level];
                                            @endphp

                                            @if($value === '✓')
                                                <span class="text-success text-lg">✓</span>
                                            @elseif($value === '—')
                                                <span class="opacity-50">—</span>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tr>
                            <td></td>

                            @foreach(config('plans.plans') as $level => $plan)
                                <td class="text-center {{ $level === $currentPlanLevel ? 'bg-success/10' : '' }}">
                                    @if($level === $currentPlanLevel)

                                        <span class="text-black badge badge-success">
                                            {{ __('Current') }}
                                        </span>
                                    @elseif($level < $currentPlanLevel)

                                    @else

                                        <x-button
                                            label="{{ __('Upgrade') }}"
                                            icon="o-chevron-double-up"
                                            class="bg-[#00bafe] btn-sm"
                                            wire:click="selectPlan({{ $level }})"
                                        />

                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>

                
                <x-slot:actions>
                    <x-button label="{{ __('View plans details') }}" class="btn-sm" />
                </x-slot:actions>
            </x-card>
        </x-tab>
    </x-tabs>

    </div>
</div>