<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Intervention\Image\Drivers\GD\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use App\Models\Wall;
use App\Models\Image;
use App\Models\Submitter;
use Livewire\Attributes\Title;

new 
#[Title('Post an image')]

class extends Component {

    use Toast, WithFileUploads;

    public Wall $wall;
    public ?Submitter $submitter = null;

    public $image;
    public string $caption = '';
    public string $background = '';
    public ?string $name = '';
    public ?string $email = '';
    public ?string $terms = '';
    public string $posting_page_font_link = '';
    public string $posting_page_font_style = '';
    public ?string $visitorToken = '';
    public string $currentCard = 'loading';

    public bool $req_data_status = false;
    public bool $showMessageCard = false;
    public string $messageText = '';
    public string $messageHeader = '';

    public function mount(wall $wall)
    {
        $this->wall = $wall;

        $this->initBackground();
        $this->initFont();
        $this->initVisitorToken();
        $this->initSubmitterData();
        $this->getSubmitterRequirements();
        $this->updateCurrentCard();
    }



    private function initBackground(): void
    {
        if ($this->wall->posting_page_background_choice == 0) {    
            $this->background = 'background: ' . $this->wall->posting_page_background_color . ';';
        } else {
            $url = asset("storage/posting_page_images/background_images/{$this->wall->posting_page_background_image}");
            $this->background = "background:  no-repeat center url('{$url}'); background-size: 100% 100%;";
        }
    }

    private function initFont(): void     
    {    // GOOGLE FONT URL :
        if (!$this->wall->posting_page_font) return;
            $font = $this->wall->posting_page_font;
            $this->posting_page_font_link = "https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $font) . "&display=swap";
            $this->posting_page_font_style = "font-family: {$font}, sans-serif;";
    }

    private function initVisitorToken(): void
    {
        $this->visitorToken = request()->cookie('visitor_token') ?? (string) \Str::uuid();
        cookie()->queue(cookie('visitor_token', $this->visitorToken, 60 * 24 * 31, null, null, false, true));
    }

    private function initSubmitterData(): void
    {
        if ($submitterId = request()->cookie('submitter_id')) {
            $this->submitter = Submitter::find($submitterId);
            $this->name = $this->submitter?->name ?? '';
            $this->email = $this->submitter?->email ?? '';
        }
    }

private function getSubmitterRequirements(): void
{
    $nameOk = !$this->wall->ask_name_submitter || !empty($this->name);
    $emailOk = !$this->wall->ask_email_submitter || !empty($this->email);

    $this->req_data_status = $nameOk && $emailOk;
}


    private function canPostImage(): bool
    {
        $max = $this->wall->max_images_submitter;

        if (is_numeric($max)) {

            // If null : no limit
            if (is_null($max)) {
                return true;
            }

            if ($max === 0) {
                $this->showMessage('Posting is blocked by the admin', 'Sorry');
                return false;
            }

            $count = Image::where('wall_id', $this->wall->id)
                ->where('permanent', true)
                ->where('visitor_token', $this->visitorToken)
                ->count();

            if ($count >= $max) {
                $this->showMessage('You have reached the maximum number of images allowed', 'Sorry');
                return false;
            }
        }

        return true;
    }

    private function showMessage(string $message, string $title): void
    {
        $this->messageText = __($message);
        $this->messageHeader = __($title);
        $this->showMessageCard = true;
        $this->currentCard = 'message';
    }

    private function updateCurrentCard(): void
    {
        if (!$this->canPostImage()) {
            $this->currentCard = 'message';
        } elseif (!$this->req_data_status) {
            $this->currentCard = 'submitter';
        } else {
            $this->currentCard = 'upload';
        }
    }




    public function saveSubmitterData()
    {

        // Build validation rules dynamically
        $rules = [
            'name' => ($this->wall->require_name_submitter ? 'required|' : 'nullable|') . 'string|max:50',
            'email' => ($this->wall->require_email_submitter ? 'required|' : 'nullable|') . 'email|max:150',
        ];

        // Validate data
        $data = $this->validate($rules);

        if ($this->submitter) {
            $this->submitter->fill($data);

            // Check if there's some changes
            if ($this->submitter->isDirty()) {
                $this->submitter->save();
                $this->req_data_status = true;
            }
        } else {
            // Creating new submitter
            $this->submitter = Submitter::create($data);
            cookie()->queue(cookie('submitter_id', $this->submitter->id, 60 * 24 * 31, null, null, false, true));
            $this->req_data_status = true;
        }
        $this->req_data_status = true;
        $this->updateCurrentCard();
    }




    public function uploadImage()
    {

        if (!$this->canPostImage()) return;

        $data = $this->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:' . $this->wall->caption_max_characters,
            'terms' => 'accepted',
        ]);
    
        // Saving full size image
        $path = $this->image->store('walls_images/images_submitters', 'public');
        $originalFilename = basename($path);

         // Creating file names for WebP and thumbnail
        $baseName = pathinfo($originalFilename, PATHINFO_FILENAME);
        $webpFilename = $baseName . '.webp';

        // Saving in database first, to get the $parent model
        $parent = Image::create([
            'wall_id' => $this->wall->id,
            'name' => $originalFilename,
            'webp_name' => $webpFilename,
            'thumb' => $webpFilename,
            'caption' => $this->caption,
            'visitor_token' => $this->visitorToken,
            'submitter_id' => $this->submitter->id ?? null,
            'submitter_name' => $this->submitter->name ?? null,
            'permanent' => true,
        ]);

        // Generate and save WebP version
        $webpImage = InterventionImage::read($this->image->getRealPath())->encodeByExtension('webp', 80); // 80 : quality (0 to 100)
        Storage::disk('public')->put($parent->webp_full_path, $webpImage);

        // Generating thumbnail         
        // Save thumb         
        $thumbImage = InterventionImage::read($this->image->getRealPath())->scale(width: 500)->encodeByExtension('webp', 80);
        Storage::disk('public')->put($parent->thumb_full_path, $thumbImage);
        /* Older version with image manager
        // Generating thumbnail
        // create new manager instance with desired driver
        $manager = new ImageManager(new Driver());
        $image = $manager->read($this->image->getRealPath())->scale(width: 500)->encode();
        Storage::disk('public')->put('thumbs/' . basename($path), $image);*/


        if (!$this->wall->moderation) {
            // Calculating the number of iterence of non-permanent image we need to add
            $wallImagesCount = Image::where('wall_id', $this->wall->id)->where('permanent', true)->count();
            $j = round($wallImagesCount*0.2);
            for ($k = 0; $k < $j; $k++) {
                // Saving in database
                Image::create([
                    'wall_id' => $this->wall->id,
                    'parent_id' => $parent->id,
                    'name' => $originalFilename,
                    'webp_name' => $webpFilename,
                    'thumb' => $webpFilename,
                    'caption' => $this->caption,
                    'status' => 1,
                    'visitor_token' => $this->visitorToken,
                    'submitter_id' => $this->submitter->id ?? null,
                    'submitter_name' => $this->submitter->name ?? null,
                    'permanent' => false,
                ]);
            }
        }

        $this->success(__('Image added successfully!'));
        $this->reset('image', 'caption');
    }

}; 
?>
<div>

<style>
.file-input::file-selector-button {
    background-color: {{ $this->wall->posting_page_buttons_color }};
    border-color: {{ $this->wall->posting_page_buttons_color }};
    color: {{ $this->wall->posting_page_buttons_font_color }};
}
.input, .file-input {
    border-color : {{ $this->wall->posting_page_buttons_color }};
}
</style>

@if ($this->posting_page_font_link)
<link rel="stylesheet" href="{{ $this->posting_page_font_link }}"/>
@endif

<div x-data="{ currentCard: @entangle('currentCard') }"
    class="h-screen flex items-center justify-center" style="{{ $background }}">

    <div x-show="currentCard === 'loading'" class="absolute inset-0 flex items-center justify-center z-50">
        <x-loading class="loading-ring" />
    </div>


    <x-card x-show="currentCard === 'submitter'" x-cloak class="flex items-center justify-center">
        <h1 class="mb-2 text-2xl font-bold text-center" style="{{ $this->posting_page_font_style }}">{{ $this->wall->posting_page_text ?: __('Post an image') }}</h1>
        @if($this->wall->posting_page_logo && $this->wall->posting_page_logo_visibility == 1)
            <img src="storage/posting_page_images/logos/{{ $this->wall->posting_page_logo }}" class="max-w-xs mb-2 mx-auto shadow-md object-cover " />
        @endif

        <x-form wire:submit="saveSubmitterData"> 

            @if ($this->wall->ask_name_submitter && !$this->wall->require_name_submitter)
                <x-input label="{{__('Name')}}" wire:model="name" maxlength="50" inline />
            @elseif ($this->wall->ask_name_submitter && $this->wall->require_name_submitter)
                <x-input label="{{__('Name')}} *" wire:model="name" maxlength="50" inline required />
            @endif

            @if ($this->wall->ask_email_submitter && !$this->wall->require_email_submitter)
                <x-input label="{{__('Email')}}" wire:model="email" maxlength="150" inline />
            @elseif ($this->wall->ask_email_submitter && $this->wall->require_email_submitter)
                <x-input label="{{__('Email')}} *" wire:model="email" maxlength="150" inline required />
            @endif
            
            <x-slot:actions>
                <x-button label="{{__('Next')}}" icon="o-paper-airplane" spinner="saveSubmitterData" type="submit" class="btn-primary"/>
            </x-slot:actions>
        </x-form>
    </x-card>

    <x-card x-show="currentCard === 'upload'" x-cloak  class="flex items-center justify-center">
        <h1 class="text-2xl font-bold text-center" style="{{ $this->posting_page_font_style }}">{{ $this->wall->posting_page_text ?: __('Post an image') }}</h1>
        @if($this->wall->posting_page_logo && $this->wall->posting_page_logo_visibility == 1)
            <img src="storage/posting_page_images/logos/{{ $this->wall->posting_page_logo }}" class="max-w-xs mx-auto shadow-md object-cover " />
        @endif

        <!-- Submitter data display
            IF askName & askEmail = false, we don't display anything
            IF askName = true, we display Name (whatever askEmail is)
            IF askEmail = true, we display Email only if askName is false.
        -->
        @php
            $label = null;

            if ($this->wall->ask_name_submitter) {
                $label = $this->submitter?->name ?: null;
            } elseif ($this->wall->ask_email_submitter) {
                $label = $this->submitter?->email ?: null;
            }
        @endphp

        @if($label)
            <x-icon name="o-user-circle" :label="$label" />
        @endif

        <x-form wire:submit="uploadImage"> 
            <x-file wire:model="image" label="{{__('Image')}}" hint="{{__('Only image formats allowed')}}" 
            accept="image/png, image/jpeg"
            class="{{ $this->wall->posting_page_buttons_color ? 'custom-file-input' : '' }}"/>            
            <x-progress wire:loading wire:target="image" class="progress-primary h-0.5" indeterminate />

            @if($image)
                <img src="{{ $image->temporaryUrl() }}" class="max-w-[30vw] max-h-[30vh] mx-auto shadow-md object-cover " />
            @endif

            @if ($wall->allow_captions)
            <x-input label="{{__('Caption')}}" wire:model="caption" hint="Max : {{ $wall->caption_max_characters }}" maxlength="{{ $wall->caption_max_characters }}" inline />
            @endif

            <x-checkbox label="{{__('I agree with terms')}}" wire:model="terms" required />

            <x-slot:actions>
                <x-button label="{{__('Send')}}" icon="o-paper-airplane" spinner="uploadImage" type="submit"
                class="{{ $this->wall->posting_page_buttons_color ? '' : 'btn-primary' }}"
                style="{{ $this->wall->posting_page_buttons_color ? '
                border-color: ' .$this->wall->posting_page_buttons_color. '; 
                color:' .$this->wall->posting_page_buttons_font_color. '; 
                background-color:'.$this->wall->posting_page_buttons_color : '' }}" />
            </x-slot:actions>
        </x-form>
    </x-card>


    <x-card x-show="currentCard === 'message'" x-cloak class="flex flex-col items-center justify-center text-center p-6">
        <h1 class="text-2xl font-bold mb-4" style="{{ $this->posting_page_font_style }}">
            {{ $messageHeader }}
        </h1>
        <p class="text-lg mb-6">{{ $messageText }}</p>
    </x-card>

</div>

<div class="absolute bottom-2 right-2 z-50">
    @include('partials.language-switcher')
</div>

</div>