<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Wall;
use Illuminate\Support\Facades\Auth;

new
#[Title('Plans')] 
class extends Component 
{
    public $current_plan;
    public int $currentPlanLevel = 0;
    public $user = null;

    public function mount()
    {
        if (Auth::check()) {
            $this->user = auth()->user();

            // Get user's current plan
            $this->currentPlanLevel = (int) $this->user->plan_level;
            $this->current_plan = $this->user->currentPlan();
        }
    }



    
}; ?>


<div class="lg:min-h-[80vh] lg:mx-auto lg:max-w-6xl">
    <h1 class="text-2xl md:text-3xl lg:text-4xl">
        {{ __('Plans') }}
    </h1>

    <div class="my-8 overflow-hidden rounded-[2rem] border border-purple-200/70 bg-gradient-to-br from-purple-600 via-fuchsia-500 to-pink-400 p-6 text-white shadow-[0_20px_60px_-20px_rgba(168,85,247,0.45)] lg:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <h2 class="mt-2 text-3xl font-semibold sm:text-4xl">{{ __('Pick the plan that matches your event energy.') }}</h2>
                <p class="mt-3 text-sm text-white/90 sm:text-base">{{ __('From intimate gatherings to large-scale activations, we’re sure to have a plan to suit your needs.') }}</p>
            </div>
            <div class="rounded-2xl bg-white/20 px-4 py-3 backdrop-blur">
                <p class="text-sm font-medium text-white/90">{{ __('Flexible upgrades') }}</p>
                <p class="text-2xl font-semibold">{{ __('Grow as you go') }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 pt-2 lg:grid-cols-3">
        @foreach(config('plans.plans') as $level => $plan)
            <x-card
                shadow
                class="group overflow-hidden border border-zinc-200/70 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-[0_25px_70px_-24px_rgba(236,72,153,0.35)] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:shadow-[0_25px_70px_-24px_rgba(168,85,247,0.4)]"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ $plan['name'] }}</h2>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ $plan['description'] }}</p>
                    </div>

                    @if($level === $currentPlanLevel && $this->user)
                        <span class="badge badge-success badge-sm">{{ __('Current') }}</span>
                    @endif
                </div>

                    <p style="font-family: Figtree;" class="mt-2 font-bold text-4xl tracking-tighter bg-linear-to-r from-purple-500 to-pink-300 bg-clip-text text-transparent ">
                        {{ $plan['price'] }}
                    </p>

                    @if(($level ?? '') != 0)
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ __('per event - No time limit on dashboard access') }}</p>
                    @endif

                <ul class="mt-6 space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ $plan['features']['walls'] }} {{ __('wall') }}{{ $plan['features']['walls'] > 1 ? 's' : '' }}</li>
                    <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ $plan['features']['images_per_wall'] }} {{ __('images') }}</li>

                    @if($plan['features']['advanced_settings'])
                        <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ __('Advanced settings included') }}</li>
                    @endif

                    @if($plan['features']['advanced_moderation'])
                        <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ __('Advanced moderation included') }}</li>
                    @endif

                    @if($plan['features']['live_usage_duration'] > 0)
                        <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ $plan['features']['live_usage_duration'] }} {{ __('hours live usage') }}</li>
                        <li class="flex items-center gap-2"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-gradient-to-r from-purple-500 to-pink-400"></span>{{ __('No time limit on dashboard access') }}</li>
                    @endif
                </ul>

                <x-slot:actions separator>
                    @if($level === $currentPlanLevel && $this->user)
                        <span class="badge badge-success">{{ __('Current') }}</span>
                    @elseif($level < $currentPlanLevel && $this->user)
                        <span class="text-sm text-zinc-600 dark:text-zinc-300">
                            {{ __('Included in') }} {{ $current_plan['name'] ?? $current_plan->name ?? '' }}
                        </span>
                    @elseif(!$this->user)
                        <x-button
                            label="{{ __('Let\'s go !') }}"
                            icon="o-rocket-launch"
                            link="{{ route('login') }}"
                            class="btn-sm rounded-full border-none bg-gradient-to-r from-purple-500 to-pink-400 text-white hover:from-fuchsia-500 hover:to-pink-500"
                        />
                    @else
                        <x-button
                            label="{{ __('Upgrade') }}"
                            icon="o-chevron-double-up"
                            class="btn-sm rounded-full border-none bg-gradient-to-r from-purple-500 to-pink-400 text-white hover:from-fuchsia-500 hover:to-pink-500"
                            wire:click="selectPlan({{ $level }})"
                        />
                    @endif
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
</div>