<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
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

    public $selectedTab = 'plan-tab';
    public $current_plan;
    public $headers = [];
    public $plans_table = [];
    public int $currentPlanLevel = 0;
    public string $current_plan_settings_level = "Basic";
    public ?string $liveUsageStartsAt = null;
    public string $userTimezone = 'UTC';

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

    public function scheduleLiveUsage(bool $startNow = false): void
    {
        $user = auth()->user();

        if ($user->live_usage_ends_at && $user->live_usage_ends_at->isFuture()) {
            $this->addError('liveUsageStartsAt', __('Live usage has already been scheduled and cannot be restarted.'));
            return;
        }

        if ($startNow) {
            $startsAt = now()->utc();
        } else {
            $this->validate([
                'liveUsageStartsAt' => ['required', 'date_format:Y-m-d\\TH:i'],
            ]);
            $startsAt = Carbon::parse($this->liveUsageStartsAt, $this->displayTimezone)->utc();

            if ($startsAt->isBefore(now()->utc())) {
                $this->addError('liveUsageStartsAt', __('The start date and time must be now or in the future.'));
                return;
            }
        }

        try {
            $user->scheduleLiveUsageSession($startsAt);
        } catch (\LogicException $exception) {
            $this->addError('liveUsageStartsAt', __($exception->getMessage()));
            return;
        }

        $this->liveUsageStartsAt = null;
        $this->success($startsAt->isFuture()
            ? __('Live usage scheduled.')
            : __('Live usage started. It cannot be paused.'));
    }

    public function setUserTimezone(string $timezone): void
    {
        if (in_array($timezone, timezone_identifiers_list(), true)) {
            $this->userTimezone = $timezone;
        }
    }

    public function getDisplayTimezoneProperty(): string
    {
        return in_array($this->userTimezone, timezone_identifiers_list(), true)
            ? $this->userTimezone
            : 'UTC';
    }

    public function getLiveUsageStateProperty(): string
    {
        $user = auth()->user();

        if (!$user->live_usage_starts_at) {
            return 'not_started';
        }

        if ($user->live_usage_starts_at->isFuture()) {
            return 'scheduled';
        }

        return $user->hasActiveLiveUsageSession() ? 'active' : 'expired';
    }

    public function getLiveUsageHoursLeftProperty(): string
    {
        $user = auth()->user();
        $duration = $user->getLiveUsageHoursGranted();
        $durationLabel = $duration === '∞'
            ? __('Unlimited')
            : $duration . ' ' . ($duration == 1 ? __('hour') : __('hours'));

        return match ($this->liveUsageState) {
            'not_started' => $durationLabel,
            'scheduled' => $user->live_usage_starts_at && $user->live_usage_ends_at
                ? $this->formatScheduledLiveUsageHours($user->live_usage_starts_at, $user->live_usage_ends_at)
                : __('Unlimited'),
            'active' => $user->live_usage_ends_at
                ? $this->formatRemainingLiveUsage($user->live_usage_ends_at)
                : __('Unlimited'),
            'expired' => $durationLabel,
            default => '00',
        };
    }

    private function formatRemainingLiveUsage($endsAt): string
    {
        return $this->formatLiveUsageDuration((int) now()->diffInSeconds($endsAt));
    }

    private function formatScheduledLiveUsageHours($startsAt, $endsAt): string
    {
        $hours = intdiv((int) $startsAt->diffInSeconds($endsAt), 3600);

        return $hours . ' ' . ($hours === 1 ? __('hour') : __('hours'));
    }

    private function formatLiveUsageDuration(int $seconds): string
    {
        $secondsRemaining = max(0, $seconds);
        $hours = intdiv($secondsRemaining, 3600);
        $minutes = intdiv($secondsRemaining % 3600, 60);
        $seconds = $secondsRemaining % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function render()
    {
        return view('livewire.users.profile');
    }
}; ?>

<div class="min-h-screen px-2 sm:px-4">
    <div class="w-full max-w-3xl mx-auto min-w-0">

    <x-tabs
        wire:model="selectedTab"
        label-div-class="flex w-full max-w-full mx-auto flex-col divide-y divide-base-content/10 sm:flex-row sm:divide-y-0 sm:overflow-x-auto sm:whitespace-nowrap scrollbar-hide"
        label-class="w-full px-3 py-3 text-left text-lg sm:w-auto sm:py-2 sm:text-2xl lg:text-3xl font-bold font-[Figtree] cursor-pointer"
        class="min-w-0 mx-auto"
    >

        <!-- PLAN TAB -->
        <x-tab
            name="plan-tab"
            label="{{ __('Your current plan') }}: {{ __($current_plan['name']) }}"
            class="flex min-w-0 flex-col items-center gap-6"
        >

            <x-card shadow separator class="w-full max-w-full mx-auto" title="{{ __('Usage and limits') }}">
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

                    <div wire:poll.1s.visible>
                        <x-stat
                            title="{{ __('Live usage available') }}"
                            value="{{ $this->liveUsageHoursLeft }}"
                            icon="o-clock"
                            color="text-primary"
                            class="font-bold text-xl"/>
                    </div>
                </div>
            </x-card>

            <div x-data x-init="$wire.setUserTimezone(Intl.DateTimeFormat().resolvedOptions().timeZone)">
            <x-card shadow separator class="w-full max-w-full mx-auto" title="{{ __('Live usage') }}">
                @php
                    $liveUsageDuration = auth()->user()->getLiveUsageHoursGranted();
                    $liveUsageState = $this->liveUsageState;
                @endphp

                @if ($liveUsageState === 'not_started')
                    @if ($liveUsageDuration == 0)
                        <p>{{ __('Your current plan does not include live submissions.') }}</p>
                    @else
                        <p class="mb-4">{{ __('Choose when your live submission time begins. Once started, the timer cannot be paused.') }}</p>
                        <x-input
                            type="datetime-local"
                            label="{{ __('Start date and time') }}"
                            wire:model="liveUsageStartsAt"
                        />
                        @error('liveUsageStartsAt')
                            <div class="alert alert-error mt-3">{{ $message }}</div>
                        @enderror
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <x-button
                                label="{{ __('Schedule start') }}"
                                icon="o-calendar"
                                class="btn-primary"
                                wire:click="scheduleLiveUsage"
                            />
                            <x-button
                                label="{{ __('Start now') }}"
                                icon="o-play"
                                class="btn-outline"
                                wire:click="scheduleLiveUsage(true)"
                            />
                        </div>
                    @endif
                @elseif ($liveUsageState === 'scheduled')
                    <div class="alert alert-info">
                        {{ __('Live submissions are scheduled to start at :date.', ['date' => auth()->user()->live_usage_starts_at->copy()->timezone($this->displayTimezone)->format('d/m/Y, H:i')]) }}
                        @if (auth()->user()->live_usage_ends_at)
                            {{ __('And will end at :date.', ['date' => auth()->user()->live_usage_ends_at->copy()->timezone($this->displayTimezone)->format('d/m/Y,  H:i')]) }}
                        @else
                            {{ __('Unlimited live submissions are scheduled.') }}
                        @endif
                    </div>
                @elseif ($liveUsageState === 'active')
                    <div class="alert alert-success">
                        @if (auth()->user()->live_usage_ends_at)
                            {{ __('Live submissions are active until :date. The timer cannot be paused.', ['date' => auth()->user()->live_usage_ends_at->copy()->timezone($this->displayTimezone)->format('d/m/Y, H:i')]) }}
                        @else
                            {{ __('Unlimited live submissions are active.') }}
                        @endif
                    </div>
                @else
                <p>{{ __('Ended on :date at :time', [
                        'date' => auth()->user()->live_usage_ends_at->copy()->timezone($this->displayTimezone)->format('d/m/Y'),
                        'time' => auth()->user()->live_usage_ends_at->copy()->timezone($this->displayTimezone)->format('H:i'),
                    ]) }}</p>
                    <div class="mt-2 alert alert-warning">
                        {{ __('This live usage period has ended. Wall display remain available, but new submissions are closed.') }}
                    </div>

                <!-- Give the option to start a new session if the live_usage_hours_granted allows it -->
                    @if ($liveUsageDuration > 0)
                        <hr class="my-3 border-base-content/10">
                        <p class="text-lg text-base-content/70 mb-2">
                            {{ __('Choose when your next live submission time begins. Once started, the timer cannot be paused.') }}
                        </p>  
                        <x-input
                            type="datetime-local"
                            label="{{ __('Start date and time') }}"
                            wire:model="liveUsageStartsAt"
                        />
                        @error('liveUsageStartsAt')
                            <div class="alert alert-error mt-3">{{ $message }}</div>
                        @enderror
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <x-button
                                label="{{ __('Schedule start') }}"
                                icon="o-calendar"
                                class="btn-primary"
                                wire:click="scheduleLiveUsage"
                            />
                            <x-button
                                label="{{ __('Start now') }}"
                                icon="o-play"
                                class="btn-outline"
                                wire:click="scheduleLiveUsage(true)"
                            />
                        </div>
                    @endif


                @endif
            </x-card>
            </div>

            <x-card shadow separator class="w-full mx-auto max-w-full h-full" title="{{ __('Plans') }}">
                    <x-slot:menu>
                        <x-button label="{{ __('View plans details') }}" class="btn-sm" link="{{ route('plans') }}"/>
                    </x-slot:menu>

                <div class="md:hidden mt-4 w-full space-y-3">
                    @foreach(config('plans.plans') as $level => $plan)
                        <div class="rounded-lg border border-base-content/10 p-4 {{ $level === $currentPlanLevel ? 'bg-success/10 ring-1 ring-success/30' : 'bg-base-100' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-lg break-words">{{ __($plan['name']) }}</h3>
                                    @if($level === $currentPlanLevel)
                                        <span class="badge badge-success badge-sm mt-1">{{ __('Current') }}</span>
                                    @endif
                                </div>
                            </div>

                            <dl class="mt-4 divide-y divide-base-content/10 text-sm">
                                @foreach($plans_table as $row)
                                    @php
                                        $value = $row['plan_'.$level];
                                    @endphp
                                    <div class="flex items-start justify-between gap-4 py-2 first:pt-0 last:pb-0">
                                        <dt class="text-base-content/70">{{ $row['feature'] }}</dt>
                                        <dd class="shrink-0 text-right font-semibold">
                                            @if($value === '✓')
                                                <span class="text-success text-lg" aria-label="{{ __('Included') }}">✓</span>
                                            @elseif($value === '—')
                                                <span class="opacity-50" aria-label="{{ __('Not included') }}">—</span>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>

                            <div class="mt-4">
                                @if($level === $currentPlanLevel)
                                    <span class="badge badge-success">{{ __('Current') }}</span>
                                @elseif($level == 3)
                                    <x-button
                                        label="{{ __('Get a quote') }}"
                                        icon="o-pencil-square"
                                        class="w-full bg-[#00bafe] btn-sm"
                                        wire:click="selectPlan({{ $level }})"
                                    />
                                @elseif($level > $currentPlanLevel)
                                    <x-button
                                        label="{{ __('Upgrade') }}"
                                        icon="o-chevron-double-up"
                                        class="w-full bg-[#00bafe] btn-sm"
                                        wire:click="selectPlan({{ $level }})"
                                    />
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="mt-4 table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="font-bold text-black dark:text-white">{{ __('Feature') }}</th>

                                @foreach(config('plans.plans') as $level => $plan)
                                    <th
                                        class="text-center text-black dark:text-white {{ $level === $currentPlanLevel ? 'bg-success/10' : '' }}"
                                    >
                                        <div class="flex flex-col items-center">
                                            @if($level === $currentPlanLevel)
                                                <span class="badge badge-success badge-sm absolute -top-3">
                                                    {{ __('Current') }}
                                                </span>
                                            @endif

                                            <span class="font-bold">
                                                {{ __($plan['name']) }}
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
                        <tfoot>
                            <tr>
                                <td></td>

                                @foreach(config('plans.plans') as $level => $plan)
                                    <td class="text-center {{ $level === $currentPlanLevel ? 'bg-success/10' : '' }}">
                                        @if($level === $currentPlanLevel)

                                            <span class="text-black badge badge-success">
                                                {{ __('Current') }}
                                            </span>
                                        @elseif($level == 3 )
                                            <x-button
                                                label="{{ __('Get a quote') }}"
                                                icon="o-pencil-square"
                                                class="w-full min-w-[8rem] bg-[#00bafe] btn-sm h-auto min-h-10 whitespace-normal px-2 text-center leading-tight"
                                                link="{{ route('contact') }}"
                                            />
                                        @elseif($level < $currentPlanLevel)

                                        @else

                                            <x-button
                                                label="{{ __('Upgrade') }}"
                                                icon="o-chevron-double-up"
                                                class="w-full min-w-[8rem] bg-[#00bafe] btn-sm h-auto min-h-10 whitespace-normal px-2 text-center leading-tight"
                                                wire:click="selectPlan({{ $level }})"
                                            />

                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>

                
                <x-slot:actions>
                    <x-button label="{{ __('View plans details') }}" class="btn-sm" link="{{ route('plans') }}"/>
                </x-slot:actions>
            </x-card>
        </x-tab>

        <!-- PROFILE TAB -->
        <x-tab name="profile-tab" label="{{ __('Your profile') }}" class="flex min-w-0 flex-col">
            {{-- HEADER + PROGRESS --}}
            <div class="mb-6 w-full max-w-full min-w-0 mx-auto">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ __('Profile completion') }}</span>
                        <span>{{ round($this->completion) }}%</span>
                    </div>

                    <progress class="progress progress-primary w-full" value="{{ $this->completion }}" max="100"></progress>
                </div>
            </div>

            {{-- INCENTIVE MESSAGE --}}
            @if ($this->completion < 100)
                <div class="alert alert-info mb-6 max-w-full min-w-0 mx-auto items-start text-sm sm:text-base">
                    {{ __('Complete your profile to improve your experience!') }}
                </div>
            @endif

            <x-form wire:submit="updateProfile">

                {{-- PERSONAL INFORMATION --}}
                <x-card class="mb-6 max-w-full mx-auto" shadow separator>
                    <x-slot:title>
                        <div class="flex items-center gap-2">
                            <x-icon name="o-user" class="w-5 h-5" />
                            <span>{{ __('Personal information') }}</span>
                        </div>
                    </x-slot:title>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <x-input label="{{__('First name')}}" wire:model="first_name" placeholder="{{ __('First name') }}" icon="o-user" inline/>
                        <x-input label="{{__('Name')}}" wire:model="name" placeholder="{{ __('Name') }}" icon="o-user" inline/>
                    </div>

                    <x-input label="{{__('E-mail')}}" wire:model="email" placeholder="{{ __('E-mail') }}" icon="o-envelope" inline/>
                    @if (! $emailVerified)
                        <div class="alert alert-warning mt-4 items-start text-sm sm:text-base">
                            {{ __('Your email is not verified. Please check your email box or get a new verification link.') }}
                            
                            <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-link h-auto min-h-0 whitespace-normal text-left">
                                {{ __('Resend verification email') }}
                            </a>
                        </div>
                    @endif
                </x-card>

                {{-- SECURITY --}}
                <x-card class="mb-6 max-w-full mx-auto" shadow separator>
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

                    <div class="mt-2 text-left sm:text-right">
                        <x-button label="{{__('Forgot your password?')}}" class="btn-ghost max-w-full whitespace-normal" link="/forgot-password" />
                    </div>

                </x-card>

                {{-- ACTION --}}
                <div class="flex justify-stretch sm:justify-center">
                    <x-button label="{{__('Save changes')}}" 
                            type="submit" 
                            icon="o-check" 
                            class="btn-primary w-full px-6 sm:w-auto" />
                </div>

            </x-form>
        </x-tab>


    </x-tabs>

    </div>
</div>