<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('Contact')]
class extends Component
{
    #[Rule('required|in:quote,contact')]
    public string $requestType = 'quote';

    #[Rule('required|string|max:30')]
    public string $name = '';

    #[Rule('required|email|max:100')]
    public string $email = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('nullable|date|after_or_equal:today')]
    public string $eventDate = '';

    #[Rule('nullable|integer|min:1|max:1000000')]
    public string $guestCount = '';

    #[Rule('required|string|max:5000')]
    public string $message = '';
    public string $website = '';

    public array $requestTypes = [];

    public function mount(): void
    {
        $this->requestTypes = [
            ['id' => 'quote', 'name' => __('Request a quote')],
            ['id' => 'contact', 'name' => __('General contact')],
        ];

        if ($user = auth()->user()) {
            $this->name = $user->display_name ?: ($user->name ?: '');
            $this->email = $user->email;
        }
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            return;
        }

        $validated = $this->validate();

        $subject = $validated['requestType'] === 'quote'
            ? __('New quote request from :name', ['name' => $validated['name']])
            : __('New contact request from :name', ['name' => $validated['name']]);

        $body = implode(PHP_EOL, [
            $subject,
            '',
            __('Request type') . ': ' . $validated['requestType'],
            __('Name') . ': ' . $validated['name'],
            __('E-mail') . ': ' . $validated['email'],
            __('Phone') . ': ' . ($validated['phone'] ?: __('Not provided')),
            __('Event date') . ': ' . ($validated['eventDate'] ?: __('Not provided')),
            __('Number of guests') . ': ' . ($validated['guestCount'] ?: __('Not provided')),
            '',
            __('Message') . ':',
            $validated['message'],
        ]);

        Mail::raw($body, function ($mail) use ($subject, $validated): void {
            $mail->to(config('mail.contact_to'))
                ->replyTo($validated['email'], $validated['name'])
                ->subject($subject);
        });

        $this->reset(['name', 'email', 'phone', 'eventDate', 'guestCount', 'message', 'website']);
        $this->requestType = 'quote';
        session()->flash('contact_status', __('Your message has been sent. We will get back to you soon.'));
    }
}; ?>

<div class="mx-auto w-full max-w-5xl">
    <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
        <div class="rounded-2xl border border-primary/20 border-l-4 border-l-primary  p-6 text-base-content shadow-xl sm:p-8">
            <h1 class="mt-3 text-3xl font-bold text-primary sm:text-4xl">{{ __('Let\'s talk about your event') }}</h1>
            <p class="mt-4 text-sm leading-6 text-base-content/75 sm:text-base">
                {{ __('Tell us what you are planning. We can answer your questions or prepare a quote tailored to your event.') }}
            </p>

            <div class="mt-8 border-t border-primary/20 pt-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-base-100/80 text-2xl shadow-sm ring-1 ring-primary/20">
                        🇫🇷
                    </div>
                    <div>
                        <h2 class="font-semibold text-base-content">{{ __('We are based in France') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-base-content/75">
                            {{ __('Created in the French Alps. Designed for events around the world.') }}
                        </p>
                    </div>
                </div>

                <p class="mt-4 text-sm leading-6 text-base-content/75">
                    {{ __('Flashwall is run by Christian, a French freelance developer experienced in both web development and event production.') }}
                </p>
                <p class="mt-3 text-sm leading-6 text-base-content/75">
                    {{ __('Our customer support (it\'s basically Christian) is based in France, and the platform is hosted in France on OVH infrastructure.') }}
                </p>
            </div>
        </div>

        <x-card shadow separator class="w-full" title="{{ __('Contact us') }}" subtitle="{{ __('Fields marked with * are required.') }}">
            @if (session('contact_status'))
                <div class="alert alert-success mb-6 text-sm">
                    <x-icon name="o-check-circle" class="h-5 w-5" />
                    <span>{{ session('contact_status') }}</span>
                </div>
            @endif

            <x-form wire:submit="submit">
                <x-select
                    label="{{ __('What can we help you with?') }}"
                    wire:model.live="requestType"
                    :options="$requestTypes"
                    option-label="name"
                    option-value="id"
                    icon="o-chat-bubble-left-right"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input label="{{ __('Name') }} *" wire:model="name" icon="o-user" />
                    <x-input label="{{ __('E-mail') }} *" type="email" wire:model="email" icon="o-envelope" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input label="{{ __('Phone') }}" wire:model="phone" icon="o-phone" />
                    <x-input label="{{ __('Event date') }}" type="date" wire:model="eventDate" icon="o-calendar-days" />
                </div>

                @if ($requestType === 'quote')
                    <x-input label="{{ __('Number of guests') }}" type="number" min="1" wire:model="guestCount" icon="o-user-group" />
                @endif

                <x-textarea
                    label="{{ __('Message') }} *"
                    wire:model="message"
                    placeholder="{{ __('Tell us about your event or ask your questions...') }}"
                    rows="5"
                />

                <div class="hidden" aria-hidden="true">
                    <x-input wire:model="website" tabindex="-1" autocomplete="off" />
                </div>

                <x-slot:actions>
                    <x-button label="{{ __('Send') }}" type="submit" icon="o-paper-airplane" class="btn-primary w-full sm:w-auto" spinner="submit" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>