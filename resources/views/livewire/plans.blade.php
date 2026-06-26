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


<div class="lg:min-h-[80vh]">  
    <x-header title="{{ __('Plans') }}" use-h1 />

        <div class="grid gap-6 pt-4 lg:grid-cols-3">
            @foreach(config('plans.plans') as $level => $plan)
                <x-card shadow>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-base-content">{{ $plan['name'] }}</h2>
                            <p class="mt-2 text-sm text-base-content/70">{{ $plan['description'] }}</p>
                        </div>

                        @if($level === $currentPlanLevel && $this->user)
                            <span class="badge badge-success badge-sm">{{ __('Current') }}</span>
                        @endif
                    </div>

                    <div class="mt-6 rounded-2xl bg-white p-5">
                        <p class="text-4xl font-bold text-base-content">{{ $plan['price'] }}</p>
                    </div>

                    <ul class="mt-6 space-y-3 text-sm text-base-content/80">
                        <li>{{ $plan['features']['walls'] }} {{ __('wall') }}{{ $plan['features']['walls'] > 1 ? 's' : '' }}</li>
                        <li>{{ $plan['features']['images_per_wall'] }} {{ __('images') }}</li>

                        @if($plan['features']['advanced_settings'])
                            <li>{{ __('Advanced settings included') }}</li>
                        @endif

                        @if($plan['features']['advanced_moderation'])
                            <li>{{ __('Advanced moderation included') }}</li>
                        @endif

                        @if($plan['features']['live_usage_duration'] > 0)
                            <li>{{ $plan['features']['live_usage_duration'] }} {{ __('hours live usage') }}</li>
                        @endif
                    </ul>

                    <x-slot:actions separator>
                        @if($level === $currentPlanLevel && $this->user)
                            <span class="badge badge-success">{{ __('Current') }}</span>
                        @elseif($level < $currentPlanLevel && $this->user)
                            <span class="text-sm text-base-content/70">
                                {{ __('Included in') }} {{ $current_plan['name'] ?? $current_plan->name ?? '' }}
                            </span>
                        @elseif(!$this->user)
                            <x-button
                                label="{{ __('Let’s go !') }}"
                                icon="o-rocket-launch"
                                link="{{ route('login') }}"
                            />
                        @else
                            <x-button
                                label="{{ __('Upgrade') }}"
                                icon="o-chevron-double-up"
                                class="bg-[#00bafe] btn-sm"
                                wire:click="selectPlan({{ $level }})"
                            />
                        @endif
                    </x-slot:actions>
                </x-card>
            @endforeach
        </div>
</div>