<?php

use App\Models\Wall;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Mary\Traits\Toast;

// For QR Code
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

new
#[Title('Settings')]

class extends Component {
    use Toast, WithFileUploads;
    
    public Wall $wall;
    
    // Var to manage advanced settings, depending on the user's plan.
    public bool $hasAdvancedSettings = false;

    // Step to display the different setting cards.
    public int $step = 1;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $max_images_submitter;
    public string $capture_mode;
    public bool $ask_name_submitter = false;
    public bool $require_name_submitter = false;
    public bool $ask_email_submitter = false;
    public bool $require_email_submitter = false;
    public bool $require_avatar_submitter = false;
    public bool $submitter_name_on_wall = false;
    public bool $caption_on_wall = false;
    public bool $allow_captions = false;
    public bool $moderation = false;
    public string $background_color;
    public int $background_choice;
    public string $background_image;
    public $new_background_image;
    public int $duration;
    public string $transition;
    public int $caption_max_width;
    public int $layout;
    public string $caption_font_unit;
    public int $caption_font_size;
    public int $submitter_name_font_size;
    public int $margin_top;
    public int $margin_bottom;
    public int $margin_left;
    public int $margin_right;
    public string $caption_font_color;
    public string $submitter_name_font_color;
    public string $caption_background_color;
    public int $caption_background_opacity;
    public int $caption_max_characters;

    public ?string $posting_page_text;
    public bool $posting_page_text_visibility;
    public string $posting_page_end_title;
    public string $posting_page_end_text;
    public ?string $posting_page_font = null;
    public ?string $posting_page_buttons_color = null;
    public ?string $posting_page_buttons_font_color = null;
    public string $posting_page_logo;
    public $new_posting_page_logo;
    public bool $posting_page_logo_visibility;
    public string $posting_page_background_color;
    public string $posting_page_background_image;
    public $new_posting_page_background_image;
    public string $posting_page_background_choice;
   
    public string $lastSavedSlug;
    public string $qr_code_position = 'bottom-right';
    public string $qr_code_color = '#000000';
    public int $qr_code_size = 12;
    public array $background_choice_options = [];
    
    public array $googleFonts = [];

    public function mount(Wall $wall)
    {
        $this->hasAdvancedSettings = auth()->user()->hasFeature('advanced_settings');
        
        $this->wall = $wall;
        $this->name = $wall->name;
        $this->slug = $wall->slug;
        if($wall->description) {
            $this->description = $wall->description;
        }
        if($wall->max_images_submitter) {
            $this->max_images_submitter = $wall->max_images_submitter;
        }
        $this->capture_mode = $wall->capture_mode;
        $this->ask_name_submitter = $wall->ask_name_submitter;
        $this->require_name_submitter = $wall->require_name_submitter;
        $this->ask_email_submitter = $wall->ask_email_submitter;
        $this->require_email_submitter = $wall->require_email_submitter;
        $this->require_avatar_submitter = $wall->require_avatar_submitter;
        $this->submitter_name_on_wall = $wall->submitter_name_on_wall;
        $this->caption_on_wall = $wall->caption_on_wall;
        $this->allow_captions = $wall->allow_captions;
        $this->moderation = $wall->moderation;
        $this->duration = $wall->duration;
        $this->transition = $wall->transition;
        $this->background_color = $wall->background_color;
        $this->background_choice = $wall->background_choice;
        $this->background_image = $wall->background_image;
        $this->caption_max_width = $wall->caption_max_width;
        $this->layout = $wall->layout;
        $this->caption_font_unit = $wall->caption_font_unit;
        $this->caption_font_size = $wall->caption_font_size;
        $this->submitter_name_font_size = $wall->submitter_name_font_size;
        $this->margin_top = $wall->margin_top;
        $this->margin_bottom = $wall->margin_bottom;
        $this->margin_left = $wall->margin_left;
        $this->margin_right = $wall->margin_right;
        $this->caption_font_color = $wall->caption_font_color;
        $this->submitter_name_font_color = $wall->submitter_name_font_color;
        $this->caption_background_color = $wall->caption_background_color;
        $this->caption_background_opacity = $wall->caption_background_opacity;
        $this->caption_max_characters = $wall->caption_max_characters;

        // Custom style and images for create-image page
        $this->posting_page_text = $wall->posting_page_text;
        $this->posting_page_text_visibility = $wall->posting_page_text_visibility;
        $this->posting_page_end_title = __($wall->posting_page_end_title);
        $this->posting_page_end_text = __($wall->posting_page_end_text);
        $this->posting_page_font = $wall->posting_page_font;
        $this->posting_page_buttons_color = $wall->posting_page_buttons_color;
        $this->posting_page_buttons_font_color = $wall->posting_page_buttons_font_color;
        $this->posting_page_logo = $wall->posting_page_logo;
        $this->posting_page_logo_visibility = (bool) $wall->posting_page_logo_visibility;
        $this->posting_page_background_color = $wall->posting_page_background_color;
        $this->posting_page_background_image = $wall->posting_page_background_image;
        $this->posting_page_background_choice = $wall->posting_page_background_choice;
        $this->qr_code_position = $wall->qr_code_position;
        $this->qr_code_color = $wall->qr_code_color;
        $this->qr_code_size = $wall->qr_code_size;

        // Var for the Sharing card, copy to clipboard. 
        $this->lastSavedSlug = $wall->slug;

        // Options for background choice radio input 
        $this->background_choice_options = [
            ['custom_key' => 1 , 'name' => 'Image'],
            ['custom_key' => 0 , 'name' => __('Color')],
        ];

        $this->loadGoogleFonts();
    }

    public function loadGoogleFonts(): void
    {
        Cache::forget('google_fonts');
        $this->googleFonts = Cache::remember('google_fonts', 60, function () {
            $apiKey = env('GOOGLE_FONTS_API_KEY');
            if (!$apiKey) {
                return [];
            }

            $url = "https://www.googleapis.com/webfonts/v1/webfonts?key=$apiKey&sort=popularity";

            $response = @file_get_contents($url);
            if (!$response) {
                return [];
            }

            $data = json_decode($response, true);
            if (!isset($data['items'])) {
                return [];
            }


        $fonts  = collect($data['items']);
        
        return $fonts
            ->take(100)
            ->map(fn ($font) => [
                'custom_key' => $font['family'],
                'name' => $font['family'],
                'style' => "font-family:'{$font['family']}',sans-serif;", // Inline style
                'google_url' => "https://fonts.googleapis.com/css2?family=" 
                    . urlencode($font['family']) 
                    . "&display=swap"
            ])
            ->values()
            ->toArray();

        });
    }


    // Computed property to get the last saved slug for the copy to clipboard functionality.
    public function getDisplayImageUrlProperty(): string
    {
        return route('slideshow', ['wall' => $this->lastSavedSlug]);
    }
    public function getCreateImageUrlProperty(): string
    {
        return route('create-image', ['wall' => $this->lastSavedSlug]);
    }



    public function getCreateImageQrCodeProperty(): string
    {
        $color = $this->qr_code_color ?? '#000000';
        [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");

        $pngData = QrCode::format('svg')->size(200)->color($r, $g, $b)->generate($this->CreateImageUrl);
        // for png : return base64_encode($pngData);
        return $pngData;
    }
    
    public function downloadQrCode($qrCodeFormat)
    {
        // List of allowed format 
        $allowed = ['png', 'svg', 'eps'];

        if (!in_array($qrCodeFormat, $allowed)) {
            abort(400, 'Format not supported');
        }

        $qr = QrCode::format($qrCodeFormat)
            ->size(600)
            ->margin(1)
            ->generate($this->CreateImageUrl);

        // MIME types
        $mimeTypes = [
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'eps' => 'application/postscript',
        ];

        $filename = 'Flashwall_' . $qrCodeFormat . 'QR_Code_' . $this->lastSavedSlug . '.' . $qrCodeFormat;

        return Response::streamDownload(function () use ($qr) {
            echo $qr;
        }, $filename, [
            'Content-Type' => $mimeTypes[$qrCodeFormat],
        ]);
    }


    /*
    *
    *   Updates general settings
    *
    */
    public function updateGeneralSettings()
    {
        $data = $this->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
            'moderation' => 'boolean',
        ]);

    
        // Filtrer uniquement les champs modifiés
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));

            // Refresh navigation when a wall name is updated.
            $this->dispatch('refreshNavigation');
        } else {
            $this->warning(__('No change detected!'));
        }
        
        // We go to next step
        $this->step++;
    }

    /*
    *
    *   Updates wall onboarding.
    *
    */
    public function updateOnboardingSettings()
    {
        if (! $this->hasAdvancedSettings) {
            $this->posting_page_end_title = 'Thank you!';
            $this->posting_page_end_text = 'The submission was successful.';
            $this->posting_page_font = null;

            $this->posting_page_background_choice = $this->wall->background_choice; // Mirrors slideshow style
            $this->posting_page_background_color = $this->wall->background_color; // Mirrors slideshow style
            $this->posting_page_background_image = $this->wall->background_image; // Mirrors slideshow style

            $this->posting_page_buttons_color = $this->wall->caption_background_color; // Mirrors caption style
            $this->posting_page_buttons_font_color = $this->wall->caption_font_color; // Mirrors caption style
        }

        $data = $this->validate([
            'posting_page_text' => 'string|max:155',
            'posting_page_text_visibility' => 'required|boolean',
            'posting_page_logo_visibility' => 'required|boolean',
            'new_posting_page_logo' => 'nullable|image|max:1024',
            'posting_page_end_title' => 'required|string|max:100',
            'posting_page_end_text' => 'required|string|max:255',
            'posting_page_font' => 'nullable|string|max:155',
            'posting_page_background_choice' => 'required|integer|in:0,1,2',
            'posting_page_background_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_posting_page_background_image' => 'nullable|image|max:20480',
            'posting_page_buttons_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'posting_page_buttons_font_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        if ($this->new_posting_page_logo) {
            // Deleting old image, then saving the new one.
            if ($this->wall->posting_page_logo !== 'posting_page_default_logo.png') {
                Storage::disk('public')->delete('posting_page_images/logos/' .$this->wall->posting_page_logo);
            }
            $logo_path = $this->new_posting_page_logo->store('posting_page_images/logos', 'public');
            $logo_filename = basename($logo_path);
            $this->wall->posting_page_logo = $logo_filename;
        }

        if ($this->new_posting_page_background_image && $this->hasAdvancedSettings) {
            // Deleting old image, then saving the new one.
            if ($this->wall->posting_page_background_image !== 'posting_page_default_background.png') {
                Storage::disk('public')->delete('posting_page_images/background_images/' .$this->wall->posting_page_background_image);
            }
            $background_path = $this->new_posting_page_background_image->store('posting_page_images/background_images', 'public');
            $background_filename = basename($background_path);
            $this->wall->posting_page_background_image = $background_filename;
        }
        
        // On prépare les changements sur le modèle (sauf l'image)
        // Remplit le modèle avec les données validées
        unset($data['new_posting_page_logo']);
        unset($data['new_posting_page_background_image']);
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }


    /*
    *
    *   Updates wall slideshow settings, background style and captions style.
    *
    */
    public function updateSlideshowSettings()
    {
        if (! $this->hasAdvancedSettings) {
            if ($this->layout == 0 || $this->layout == 2) {
                $this->margin_top = 5;
                $this->margin_bottom = 5;
                $this->margin_left = 5;
                $this->margin_right = 5;
                $this->qr_code_size = 17;
            }
            elseif ($this->layout == 1) {
                $this->margin_top = 5;
                $this->margin_bottom = 20;
                $this->margin_left = 5;
                $this->margin_right = 5;
                $this->qr_code_size = 12;

            }
            $this->caption_max_width = 90;
            $this->caption_background_opacity = 70;
            $this->caption_font_unit = 'px';
            $this->submitter_name_font_size = $this->caption_font_size; // Mirrors caption font size
            $this->submitter_name_font_color = $this->caption_font_color; // Mirrors caption font color
            $this->posting_page_background_choice = $this->background_choice; // Mirrors wall bg choice
            $this->posting_page_background_color = $this->background_color; // Mirrors wall bg color
            $this->wall->posting_page_background_choice = $this->background_choice; // Mirrors wall bg color
            $this->wall->posting_page_background_color = $this->background_color; // Mirrors wall bg color
        }

        $data = $this->validate([
            'duration' => 'required|integer|min:2|max:99',
            'transition' => 'required|string|in:none,fade,zoom',
            'layout' => ['required', 'integer', 'in:0,1,2'],
            'margin_top' => 'required|integer|max:90',
            'margin_bottom' => 'required|integer|max:90',
            'margin_left' => 'required|integer|max:90',
            'margin_right' => 'required|integer|max:90',
            'background_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_background_image' => 'nullable|image|max:20480',
            'background_choice' => 'required|integer|in:0,1,2',
            'caption_on_wall' => 'boolean',
            'caption_font_size' => 'required|integer|max:500',
            'caption_font_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'caption_background_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            // Advanced settings :
            'caption_max_width' => 'required|integer|max:100',
            'caption_background_opacity' => 'required|integer|max:100',
            'caption_font_unit' => 'required|string|max:10',
            'submitter_name_font_size' => 'required|integer|max:500',
            'submitter_name_font_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
 
        if ($this->new_background_image) {
            // Suppression de l'ancienne image puis sauvegarde de la nouvelle image
            if ($this->wall->background_image !== 'walls_images/background_images/default_background.jpg' && $this->wall->background_image !== 'walls_images/background_images/grid_background.jpg') {
                $currentBackgroundPath = str_contains($this->wall->background_image, '/')
                    ? $this->wall->background_image
                    : 'walls_images/background_images/' . $this->wall->background_image;
                Storage::disk('public')->delete($currentBackgroundPath);
            }
            $background_image_path = $this->new_background_image->store('walls_images/background_images', 'public');
            $this->background_image = $background_image_path;
            $this->wall->background_image = $background_image_path;

            // If user doesn't have advanced settings, we want to mirror the posting page background with the slideshow background.
            if (! $this->hasAdvancedSettings) {
                $this->posting_page_background_image = $background_image_path; // Mirrors slideshow style
                $this->wall->posting_page_background_image = $background_image_path;
            }
        }

        // Filter only modified fields
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Fills the model with the validated data
        unset($data['new_background_image']);
        $this->wall->fill($data);
    
        // Check if there are any modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
        
        // We go to next step
        $this->step++;
    }



    /*
    *
    *   Updates wall submitter settings.
    *
    */
    public function updateSubmitterSettings()
    {
        if(!$this->hasAdvancedSettings) {
            $this->require_name_submitter = $this->ask_name_submitter ? true : false;
            $this->require_email_submitter = $this->ask_email_submitter ? true : false;
            $this->require_avatar_submitter = true;
            $this->submitter_name_on_wall = true;
            $this->allow_captions = true;
            $this->caption_max_characters = 255;
            $this->capture_mode = 0;
        }

        $data = $this->validate([
            'ask_name_submitter' => 'boolean',
            'ask_email_submitter' => 'boolean',
            'require_name_submitter' => 'boolean',
            'require_email_submitter' => 'boolean',
            'max_images_submitter' => 'integer|max:99',
            'require_avatar_submitter' => 'boolean',
            'submitter_name_on_wall' => 'boolean',
            'allow_captions' => 'boolean',
            'caption_max_characters' => 'integer|min:10|max:255',
            'capture_mode' => 'required|integer|in:0,1,2',
        ]);
        
        // Filter only modified fields
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Fills the model with the validated data
        $this->wall->fill($data);
    
        // Check if there are any modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }


    /*
    *
    *   Updates wall slug and QR Code position.
    *
    */
    public function updateShareOptions()
    {
        if(!$this->hasAdvancedSettings) {
            $this->slug = $this->wall->slug; // Reset slug to the current one, as user can't change it.
            $this->qr_code_color = "#000000"; // Reset QR code color to the default one, as user can't change it.
            $this->qr_code_size = 12; // Reset QR code size to the default one, as user can't change it.
        }

        $data = $this->validate([
            'slug' => [
                'required',
                'string',
                'max:35',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:walls,slug,' . $this->wall->id,
            ],
            'qr_code_position' => 'required|string|in:none,bottom-right,bottom-left,top-right,top-left',
            'qr_code_color' => [
                'required',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            ],
            'qr_code_size' => 'required|integer|min:5|max:100',
        ]); 
    
        // Check if slug changed before fill/save
        $slugChanged = $this->wall->slug !== $data['slug'];

        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));

            // Redirect ONLY if slug changed
            if ($slugChanged) {
                return redirect()->route('setup-wall', $this->wall);
            }
        } else {
            $this->warning(__('No change detected!'));
        }
    }

    public function deleteWall()
    {
        if ($this->wall->user_id !== Auth::id()) {
            abort(403, 'Forbidden action!');
        }

        $this->wall->delete();
        
        $this->success(
            __('Wall successfully deleted!'),
            redirectTo: '../create-wall'
        );
    }

};

?>
<div x-data="{ step: @entangle('step') }">
    <div class="flex flex-row justify-between flex-nowrap">
        <x-header title=" {{ __('Settings') }} : {{ $wall->name }}" use-h1>
        </x-header>
        <x-button title="{{ __('Preview changes') }}" class="btn-circle" icon="o-eye" link="{{ route('slideshow.mode', ['wall' => $wall->slug, 'mode' => 'preview']) }}" external />
    </div>


    <x-badge x-show="step === 1" value="{{ __('Click on the icons to navigate') }}" 
    icon-right="o-arrow-turn-right-down" 
    class="badge-primary badge-soft rotate-270 lg:rotate-0 absolute top-50 lg:top-11 -left-30 lg:left-1/2 lg:-translate-x-1/2" />

<div class="flex flex-row lg:flex-col items-start lg:items-center w-full -mt-10">
    <ul class="steps steps-vertical lg:steps-horizontal mt-3 mb-3 shrink-2" class="indicator">
        <li class="step step-primary cursor-pointer" :class="step >= 1 ? 'step-primary' : ''" @click="step = 1">
            <span class="step-icon"><x-icon name="o-cog-6-tooth" /></span>
            <span class="hidden sm:inline">{{ __('General settings') }}</span>
        </li>
        <li class="step cursor-pointer" :class="step >= 2 ? 'step-primary' : ''" @click="step = 2">
            <span class="step-icon"><x-icon name="o-device-phone-mobile" /></span>
            <span class="hidden sm:inline">{{ __('Onboarding') }}</span>
        </li>
        <li class="step cursor-pointer" :class="step >= 3 ? 'step-primary' : ''" @click="step = 3">
            <span class="step-icon"><x-icon name="o-tv" /></span>
            <span class="hidden sm:inline">{{ __('Slideshow') }}</span>
        </li>
        <li class="step cursor-pointer" :class="step >= 4 ? 'step-primary' : ''" @click="step = 4">
            <span class="step-icon"><x-icon name="o-users" /></span>
            <span class="hidden sm:inline">{{ __('Users') }}</span>
        </li>
        <li class="step cursor-pointer" :class="step >= 5 ? 'step-primary' : ''" @click="step = 5">
            <span class="step-icon"><x-icon name="o-share" /></span>
            <span class="hidden sm:inline">{{ __('Share') }}</span>
        </li>
    </ul>

<!---------------
----  STEP 1 : General settings, including wall name, description and moderation. And Delete Wall.
----------------->
<div class="flex-1 flex justify-center mt-3 lg:mt-0 min-w-0">
    <template x-if="step === 1">
        <div class="flex justify-center items-start flex-col gap-6">
            
            <x-card title="{{ __('General settings') }}" class="min-w-48" shadow separator>
                <x-form wire:submit="updateGeneralSettings">
                    <x-input label="{{ __('Name') }}" placeholder="{{ __('Name') }}" wire:model="name" inline />
                    <x-input label="{{ __('Description') }}" placeholder="{{ __('Description') }}" wire:model="description" inline />

                    <x-menu-separator />

                    <x-toggle label="{{__('Activate moderation?')}}" wire:model="moderation" right inline/>

                    <x-slot:actions>
                        <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateGeneralSettings" />
                    </x-slot:actions>
                </x-form>
            </x-card>
            
            <x-card title='Danger Zone' class="w-96 border border-error" class="min-w-48" shadow separator>
                <p>{{__('These actions are irreversible!')}}</p>
                
                <x-menu-separator />
                <x-button label="{{__('Delete this wall')}}" icon="o-trash" class="btn btn-error"
                    wire:click="deleteWall" 
                    wire:confirm.prompt="{{ __('Are you sure? Type DELETE to confirm|DELETE') }}"
                    spinner="deleteWall" />

            </x-card>
        </div>
    </template>


<!---------------
----  STEP 2 : Onboarding settings
----------------->
    <template x-if="step === 2">
        <x-form wire:submit="updateOnboardingSettings">
            <div class="flex justify-center items-start flex-col md:flex-row gap-6">
            
            <x-card title="{{ __('Onboarding settings') }}" class="min-w-48" shadow separator>          
            
                <x-input label="{{ __('Welcome text') }}" hint="{{ __('It will also be the page title, even if the Welcome text is hidden') }}"
                placeholder="{{ __('Welcome text') }}" wire:model="posting_page_text" inline />

                <x-toggle label="{{__('Display welcome text?')}}" wire:model="posting_page_text_visibility" right inline/>
                
                <div class="max-w-full overflow-hidden" x-data="{ posting_page_logo_visibility: @entangle('posting_page_logo_visibility') }">
                    <x-toggle label="{{__('Display logo?')}}" x-model="posting_page_logo_visibility" wire:model="posting_page_logo_visibility" right inline/>

                    <div x-show="posting_page_logo_visibility == true">
                        <x-file style="max-width: 100% !important" wire:model="new_posting_page_logo" 
                            hint="{{ __('Only image formats allowed') }}"
                            accept="image/png, image/jpeg"
                        />
                        <x-progress wire:loading wire:target="new_posting_page_logo" class="progress-primary h-0.5" indeterminate />
                        @if($new_posting_page_logo)
                            <img src="{{ $new_posting_page_logo->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                        @elseif($posting_page_logo)
                            <img src="{{ asset('storage/' . $wall->posting_page_logo) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                        @endif
                    </div>
                </div>

                <x-menu-separator />

                <!-- ADVANCED ONBOARDING SETTINGS -->
                <x-advanced-settings>

                        <div class="flex flex-col gap-y-3 -mt-2 mb-2">
                            <span class="fieldset-legend text-sm font-medium">{{ __('End page content / Thank you message')}}</span>
                            <x-input :disabled="!$hasAdvancedSettings" label="{{ __('Title') }}"
                            placeholder="{{ __('Title') }}" wire:model="posting_page_end_title" inline />
                            <x-input :disabled="!$hasAdvancedSettings" label="Message" 
                            placeholder="Message" wire:model="posting_page_end_text" inline />
                        </div>

                        <!-- CUSTOM FONT SELECTOR -->
                        {{-- Load all Google Fonts once --}}
                        @foreach ($googleFonts as $font)
                            <link rel="stylesheet" href="{{ $font['google_url'] }}">
                        @endforeach


                        <!-- CUSTOM FONT SELECTOR INPUT -->
                        <div
                            x-data="{
                                selectedFont: @entangle('posting_page_font'),
                                hasAdvancedSettings: @js($hasAdvancedSettings),

                                selectFont(font) {
                                    this.selectedFont = font.name;
                                    this.open = false;
                                },

                                clearSelection() {
                                    this.selectedFont = '';
                                }
                            }"
                            class="[&_.menu]:!z-[99999]"
                            @click.capture="if (!hasAdvancedSettings) {
                                $event.stopPropagation();
                                $event.preventDefault();
                            }"
                        >
                            <x-dropdown
                                scroll
                                max-height="max-h-42"
                                class="[&.menu]:!z-[99999]">
                                <x-slot:trigger>

                                    <label class="fieldset-legend text-sm font-medium">
                                        {{ __('Posting page font') }}
                                    </label>

                                    <div
                                        tabindex="0"
                                        @click="if (!hasAdvancedSettings) return;"
                                        :class="{
                                            'ring ring-primary ring-opacity-30': open,
                                            'cursor-not-allowed bg-[#f8f8f8] dark:bg-[#191e24]': !hasAdvancedSettings,
                                            'cursor-pointer': hasAdvancedSettings
                                        }"
                                        class="flex items-center justify-between w-full input"
                                    >
                                        <span
                                            x-text="selectedFont || '{{ __('Select a font') }}'"
                                            class="truncate"
                                            :class="selectedFont
                                                ? 'text-black dark:text-gray-400'
                                                : 'text-gray-500 dark:text-gray-400'"
                                            :style="selectedFont
                                                ? `font-family: '${selectedFont}', sans-serif;`
                                                : ''"
                                        ></span>

                                        <div class="flex items-center gap-1">

                                            <template x-if="selectedFont">
                                                <button
                                                    @click.stop="if (!hasAdvancedSettings) return; clearSelection()"
                                                    type="button"
                                                    class="text-gray-500 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none"
                                                    :class="!hasAdvancedSettings
                                                        ? 'cursor-not-allowed'
                                                        : 'cursor-pointer'"
                                                    title="{{ __('Clear selection') }}"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12"
                                                        />
                                                    </svg>
                                                </button>
                                            </template>

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-gray-500 transition-transform duration-150"
                                                :class="{ 'rotate-180': open }"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>

                                        </div>
                                    </div>

                                </x-slot:trigger>


                                {{-- Font options --}}
                                @foreach ($googleFonts as $font)
                                    <li
                                        wire:key="font-{{ $font['custom_key'] }}"
                                        @click="selectFont(@js($font))"
                                        style="{{ $font['style'] }}"
                                        class="px-3 py-2 hover:bg-base-200"
                                        :class="!hasAdvancedSettings
                                                        ? 'cursor-not-allowed'
                                                        : 'cursor-pointer'"
                                    >
                                        {{ $font['name'] }}
                                    </li>
                                @endforeach

                            </x-dropdown>
                        </div>
                
                </x-advanced-settings>

            </x-card>

            <x-card title="{{ __('Onboarding page style') }}" class="min-w-48 max-w-full md:max-w-1/3" shadow separator>
                @if(!$hasAdvancedSettings)
                <x-alert title="{{ __('Advanced settings') }}" description="{{ __('These settings are not available with your current subscription.') }}" class="alert-info flex flex-wrap">
                    <x-slot:actions>
                        <x-button label="{{ __('Upgrade your plan !') }}" class="btn-sm" />
                    </x-slot:actions>
                </x-alert>

                <x-alert title="{{ __('If these settings are locked, they will inherit the background and colour choices you will make for the slideshow.') }}"
                class="alert-warning alert-soft mt-2 mb-2" icon="o-information-circle" />
                @endif


            <!-- ADVANCED ONBOARDING SETTINGS -->
                <x-advanced-settings>
                    <div class="max-w-full overflow-hidden" x-data="{ posting_page_choice: @entangle('posting_page_background_choice') }">
                        <div class="flex justify-center text-center mb-1">
                            <x-group
                            label="{{ __('Use as background') }} :"
                            :options="$background_choice_options"
                            wire:model="posting_page_background_choice"
                            :disabled="!$hasAdvancedSettings"
                            option-value="custom_key"
                            class="[&:checked]:!btn-primary btn-sm" />
                        </div>    
                        <div x-show="posting_page_choice == 0"  x-data="{ posting_page_background_color: @entangle('posting_page_background_color') }"class="flex flex-row items-end justify-between">
                            <x-input :disabled="!$hasAdvancedSettings" class="w-full" label="{!! __('Page background color')!!}" x-model="posting_page_background_color">
                                <x-slot:prefix>Hex</x-slot:prefix>
                            </x-input>

                            <input
                                type="color"
                                x-model="posting_page_background_color"
                                wire:model="posting_page_background_color"
                                @disabled(!$hasAdvancedSettings)
                                class="h-8 w-12 mb-[0.46rem] disabled:cursor-not-allowed disabled:opacity-50"
                                title="{{ __('Choose a color') }}"
                            >
                        </div>
                        @error('posting_page_background_color')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    
                        <div x-show="posting_page_choice == 1"  class="max-w-full text-center overflow-hidden">
                            <x-file :disabled="!$hasAdvancedSettings" wire:model="new_posting_page_background_image" style="max-width: 100% !important" label="{!! __('Page background image') !!}" 
                                hint="{{ __('Only image formats allowed') }}"
                                accept="image/png, image/jpeg"
                            />
                            <x-loading wire:loading wire:target="new_posting_page_background_image" class="loading-ring" indeterminate />
                            @if($new_posting_page_background_image)
                                <img src="{{ $new_posting_page_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                            @elseif($posting_page_background_image)
                                <img src="{{ asset('storage/' . $wall->posting_page_background_image) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                            @endif
                        </div>
                    </div>

                    <x-menu-separator />

                    <div x-data="{ posting_page_buttons_color: @entangle('posting_page_buttons_color') }" class="flex flex-row gap-x-3 items-end justify-between">
                        <x-input :disabled="!$hasAdvancedSettings" class="whitespace-nowrap overflow-visible" label="{{ __('Buttons color') }}" placeholder="{{ __('Buttons color') }}" x-model="posting_page_buttons_color" >
                            <x-slot:prefix>Hex</x-slot:prefix>
                            <x-slot:suffix>
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-black focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    title="{{ __('Clear color') }}"
                                    @disabled(!$hasAdvancedSettings)
                                    wire:click="$set('posting_page_buttons_color', null)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </x-slot:suffix>
                        </x-input>
                        
                        <input
                            type="color"
                            @disabled(!$hasAdvancedSettings)
                            x-bind:value="posting_page_buttons_color || '#cccccc'"
                            @input="posting_page_buttons_color = $event.target.value"
                            wire:model="posting_page_buttons_color"
                            class="h-8 w-12 mb-[0.46rem] cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            title="{{ __('Choose a color') }}"
                        >
                    </div>
                    @error('posting_page_buttons_color')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                
                    <div x-data="{ posting_page_buttons_font_color: @entangle('posting_page_buttons_font_color') }" class="flex flex-row gap-x-3 items-end justify-between">
                        <x-input :disabled="!$hasAdvancedSettings" class="whitespace-nowrap overflow-visible" label="{{ __('Buttons font color') }}" placeholder="{{ __('Buttons font color') }}" x-model="posting_page_buttons_font_color" >
                            <x-slot:prefix>Hex</x-slot:prefix>
                            <x-slot:suffix>
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-black focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    title="{{ __('Clear color') }}"
                                    @disabled(!$hasAdvancedSettings)
                                    wire:click="$set('posting_page_buttons_font_color', null)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </x-slot:suffix>
                        </x-input>

                        <input
                            type="color"
                            @disabled(!$hasAdvancedSettings)
                            x-bind:value="posting_page_buttons_font_color || '#cccccc'"
                            @input="posting_page_buttons_font_color = $event.target.value"
                            wire:model="posting_page_buttons_font_color"
                            class="h-8 w-12 mb-[0.46rem] cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            title="{{ __('Choose a color') }}"
                        >
                    </div>
                    @error('posting_page_buttons_font_color')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </x-advanced-settings>

            </x-card>
            </div>
            
            <x-slot:actions style="justify-content: space-between !important;" class="flex-wrap">
                <x-button label="{{ __('Go back') }}" @click="step = Math.max(1, step - 1)" style="align-self: flex-start !important;" type="button" icon="o-arrow-left" class="btn" spinner="updateOnboardingSettings"/>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateOnboardingSettings" />
            </x-slot:actions>
        </x-form>
    </template>


<!---------------
----  STEP 3 : Slideshow settings.
----------------->
    <template x-if="step === 3">
        <x-form wire:submit="updateSlideshowSettings">
            <div x-data="{ 'captionFontUnit': @entangle('caption_font_unit') }" class="flex w-full justify-center items-start gap-6 flex-wrap">
            <x-card title="{{ __('Slideshow settings') }}" shadow separator>
                <p class="fieldset-legend text-sm font-medium -mt-4 pb-4">{{ __('Images display') }}</p>
                <x-input type="number" label="{{ __('Time per image') }}" placeholder="{{ __('Time per image') }}" wire:model="duration" inline >
                    <x-slot:prefix>{{ __('Seconds')}}</x-slot:prefix>
                </x-input>

                @php
                    $transition_names = [
                        ['custom_key' => 'none' , 'name' => __('None')],
                        ['custom_key' => 'fade' , 'name' => 'Fade'],
                        ['custom_key' => 'zoom' , 'name' => 'Zoom'],
                    ];
                @endphp
                <x-select label="Transition" wire:model="transition" :options="$transition_names" option-label="name" option-value="custom_key" />

                <x-menu-separator />

                <p class="fieldset-legend text-sm font-medium pt-0">{{ __('Layout') }}</p>
                <div x-data="{ layout: @js($layout) }" x-effect="layout = $wire.layout" class="flex flex-wrap max-w-[100vh] lg:max-w-[25vw] items-center justify-center gap-6">
                    <!-- Layout 0: Caption slide in -->
                    <label class="cursor-pointer flex flex-col items-center gap-2">
                        <input type="radio" name="layout" value="0" wire:model="layout" class="hidden" />
                        <div class="p-4 rounded-lg transition-all bg-white" :class="layout == 0 ? 'border-2 border-primary bg-primary/10' : 'border-2 border-gray-300'">
                            <img class="h-15 w-full" src="{{ asset('storage/slideshow-layout-preview/layout_caption_slide_in.gif') }}"/>
                        </div>
                        <span class="text-sm font-medium">{{ __('Caption slide in') }}</span>
                    </label>

                    <!-- Layout 1: Caption below image -->
                    <label class="cursor-pointer flex flex-col items-center gap-2">
                        <input type="radio" name="layout" value="1" wire:model="layout" class="hidden" />
                        <div class="p-4 rounded-lg transition-all bg-white" :class="layout == 1 ? 'border-2 border-primary bg-primary/10' : 'border-2 border-gray-300'">
                            <img class="h-15 w-full" src="{{ asset('storage/slideshow-layout-preview/layout_caption_bellow_image.png') }}"/>
                        </div>
                        <span class="text-sm font-medium">{{ __('Caption below image') }}</span>
                    </label>

                    <!-- Layout 2: Caption on image -->
                    <label class="cursor-pointer flex flex-col items-center gap-2">
                        <input type="radio" name="layout" value="2" wire:model="layout" class="hidden" />
                        <div class="p-4 rounded-lg transition-all bg-white" :class="layout == 2 ? 'border-2 border-primary' : 'border-2 border-gray-300'">
                            <img class="h-15 w-full" src="{{ asset('storage/slideshow-layout-preview/layout_caption_on_image.png') }}"/>
                        </div>
                        <span class="text-sm font-medium">{{ __('Caption on image') }}</span>
                    </label>
                </div>


                    <x-menu-separator />
                
                <!-- ADVANCED LAYOUT SETTINGS -->
                <x-advanced-settings>

                    <div class="flex items-center justify-center gap-4 sm:flex-row flex-col">
                        <x-input :disabled="!$hasAdvancedSettings" class="w-20" type="number" label="{{ __('Top margin') }}" placeholder="{{ __('Top margin') }}" wire:model="margin_top" inline >
                            <x-slot:prefix>%</x-slot:prefix>
                        </x-input>
                        <x-input :disabled="!$hasAdvancedSettings" class="w-20" type="number" label="{{__('Bottom margin') }}" placeholder="{{__('Bottom margin') }}" wire:model="margin_bottom" inline >
                            <x-slot:prefix>%</x-slot:prefix>
                        </x-input>
                    </div>
                    <div class="flex items-center justify-center gap-4 sm:flex-row flex-col">
                        <x-input :disabled="!$hasAdvancedSettings" class="w-20" type="number" label="{{ __('Left margin') }}" placeholder="{{ __('Left margin') }}" wire:model="margin_left" inline >
                            <x-slot:prefix>%</x-slot:prefix>
                        </x-input>
                        <x-input :disabled="!$hasAdvancedSettings" class="w-20" type="number" label="{{__('Right margin') }}" placeholder="{{__('Right margin') }}" wire:model="margin_right" inline >
                            <x-slot:prefix>%</x-slot:prefix>
                        </x-input>
                    </div>
                </x-advanced-settings>
            </x-card>

            <x-card title="{{ __('Slideshow page style') }}" shadow separator>
                <div class="max-w-full overflow-hidden pb-1" x-data="{ wall_background_choice: @entangle('background_choice') }">
                    <div class="flex justify-center text-center mb-1">
                        <x-group
                            label="{{ __('Use as background') }} :"
                            :options="$background_choice_options"
                            wire:model="background_choice"
                            option-value="custom_key"
                            class="[&:checked]:!btn-primary btn-sm" />
                    </div>

                    <div x-show="wall_background_choice == 0" x-data="{ background_color: @entangle('background_color') }"class="flex flex-row items-end justify-between">
                        <x-input class="w-full" label="{!! __('Background color')!!}" placeholder="{!! __('Background color')!!}" x-model="background_color">
                            <x-slot:prefix>Hex</x-slot:prefix>
                        </x-input>

                        <input
                            type="color"
                            x-model="background_color"
                            wire:model="background_color"
                            class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                            title="{{ __('Choose a color') }}"
                        >
                    </div>
                    @error('background_color')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <div x-show="wall_background_choice == 1" class="max-w-full text-center overflow-hidden">
                        <x-file style="max-width: 100% !important" wire:model="new_background_image" label="{!! __('Background image') !!}" 
                            hint="{{ __('Only image formats allowed') }}"
                            accept="image/png, image/jpeg"
                        />

                        <x-loading wire:loading wire:target="new_background_image" class="loading-ring" indeterminate />
                        @if($new_background_image)
                            <img src="{{ $new_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                        @elseif($background_image)
                            <img src="{{ asset('storage/' . $wall->background_image) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                        @endif
                    </div>
                </div>
            </x-card>



            <x-card title="{{ __('Captions') }}" shadow separator>

                <x-toggle label="{{__('Display caption?')}}" wire:model="caption_on_wall" right inline/>

                <p class="fieldset-legend text-sm font-medium pb-4">{{ __('Captions font') }}</p>
                <x-input type="number" label="{{ __('Font size') }}" placeholder="{{ __('Font size') }}" wire:model="caption_font_size" inline >
                    <x-slot:prefix>
                        <span x-text="captionFontUnit === '%' ? '%' : 'Pixels'"></span>
                    </x-slot:prefix>
                </x-input>

                <div x-data="{ caption_font_color: @entangle('caption_font_color') }" class="flex flex-row items-end justify-between">
                    <x-input class="w-full" label="{{ __('Font color')}}" placeholder="{{ __('Font color')}}" x-model="caption_font_color">
                        <x-slot:prefix>Hex</x-slot:prefix>
                    </x-input>
                    
                    <input
                        type="color"
                        x-model="caption_font_color"
                        wire:model="caption_font_color"
                        class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                        title="{{ __('Choose a color') }}"
                    >
                </div>
                @error('caption_font_color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <x-menu-separator />

                <p class="fieldset-legend text-sm font-medium">{{ __('Captions box style') }}</p>
                <div x-data="{ caption_background_color: @entangle('caption_background_color') }" class="flex flex-row items-end justify-between">
                    <x-input class="w-full" label="{!! __('Background color')!!}" placeholder="{!! __('Background color')!!}" x-model="caption_background_color">
                        <x-slot:prefix>Hex</x-slot:prefix>
                    </x-input>
                    
                    <input
                        type="color"
                        x-model="caption_background_color"
                        wire:model="caption_background_color"
                        class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                        title="{{ __('Choose a color') }}"
                    >
                </div>
                @error('caption_background_color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror


                <x-menu-separator />
                
                <!-- ADVANCED CAPTION SETTINGS -->
                    <x-advanced-settings> 
                        <x-input type="number" label="{{ __('Captions box max width') }}" :disabled="!$hasAdvancedSettings" placeholder="{{ __('Captions box max width') }}" wire:model="caption_max_width" inline >
                            <x-slot:prefix>%</x-slot:prefix>
                        </x-input>

                        <div x-data="{ opacity: @entangle('caption_background_opacity') }">
                            <x-range
                            wire:model.live.debounce="caption_background_opacity"
                            min="0"
                            max="100"
                            step="10"
                            :disabled="!$hasAdvancedSettings"
                            label="{!! __('Captions box opacity level') !!}"
                            class="range-primary range-xs" />
                            <div class="text-center">
                                <p>{{ __('Selected') }} : <span x-text="opacity + '%'"></span></p>
                            </div>
                        </div>

                        <x-menu-separator />
                        
                        @php
                            $font_unit_options = [
                                ['custom_key' => 'px' , 'name' => 'Pixels'],
                                ['custom_key' => '%' , 'name' => '%'],
                            ];
                        @endphp
                        <div class="flex justify-center text-center">
                            <x-group
                                label="{{__('Font unit') }} :"
                                :options="$font_unit_options"
                                :disabled="!$hasAdvancedSettings"
                                wire:model="caption_font_unit"
                                option-value="custom_key"
                                class="[&:checked]:!btn-primary btn-sm normal-case pt-0" />
                        </div>

                        <p class="fieldset-legend text-sm font-medium mb-1">{{ __('Names font') }}</p>
                        <x-input type="number" label="{{ __('Names font size') }}" :disabled="!$hasAdvancedSettings" placeholder="{{ __('Names font size') }}" wire:model="submitter_name_font_size" inline >
                            <x-slot:prefix>
                                <span x-text="captionFontUnit === '%' ? '%' : 'Pixels'"></span>
                            </x-slot:prefix>
                        </x-input>

                        <div x-data="{ submitter_name_font_color: @entangle('submitter_name_font_color') }" class="-mt-2 flex flex-row items-end justify-between">
                            <x-input class="w-full" label="{{ __('Names font color')}}" :disabled="!$hasAdvancedSettings" placeholder="{{ __('Names font color')}}" x-model="submitter_name_font_color">
                                <x-slot:prefix>Hex</x-slot:prefix>
                            </x-input>

                            <input
                                type="color"
                                x-model="submitter_name_font_color"
                                wire:model="submitter_name_font_color"
                                @disabled(!$hasAdvancedSettings)
                                class="h-8 w-12 mb-[0.46rem] cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                                title="{{ __('Choose a color') }}"
                            >
                        </div>
                        @error('submitter_name_font_color')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </x-advanced-settings> 
                </x-card>
            </div>
            
            <x-slot:actions style="justify-content: space-between !important;" class="flex-wrap">
                <x-button label="{{ __('Go back') }}" @click="step = Math.max(1, step - 1)" style="align-self: flex-start !important;" type="button" icon="o-arrow-left" class="btn" spinner="updateSlideshowSettings"/>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateSlideshowSettings" />
            </x-slot:actions>
        </x-form>
    </template>



<!---------------
----  STEP 4 : User / Submitter settings.
----------------->
    <template x-if="step === 4">
        <x-card title="{{ __('Users') }}" class="min-w-48" shadow separator>
            <x-form wire:submit="updateSubmitterSettings">
            <p class="pb-0 label label-text font-semibold">{{__('Requested user information')}}</p>
                <div x-data="{
                    hasAdvancedSettings: @js($hasAdvancedSettings),

                    ask_name_submitter: @entangle('ask_name_submitter'),
                    ask_email_submitter: @entangle('ask_email_submitter'),

                    require_name_submitter: @entangle('require_name_submitter'),
                    require_email_submitter: @entangle('require_email_submitter'),

                    init() {
                        // Existing behavior
                        this.$watch('ask_name_submitter', value => {
                            if (!value) {
                                this.require_name_submitter = false
                            }

                            // Sync when advanced settings disabled
                            if (!this.hasAdvancedSettings) {
                                this.require_name_submitter = value
                            }
                        })

                        this.$watch('ask_email_submitter', value => {
                            if (!value) {
                                this.require_email_submitter = false
                            }

                            // Sync when advanced settings disabled
                            if (!this.hasAdvancedSettings) {
                                this.require_email_submitter = value
                            }
                        })

                        // Initial sync
                        if (!this.hasAdvancedSettings) {
                            this.require_name_submitter = this.ask_name_submitter
                            this.require_email_submitter = this.ask_email_submitter
                        }
                    }
                }" class="space-y-2">

                    <div class="flex items-start gap-4 sm:flex-row flex-col">
                        <x-checkbox label="{{ __('Name') }}" wire:model="ask_name_submitter" />
                        <x-checkbox label="{{ __('Name required') }}" wire:model="require_name_submitter"
                            x-bind:disabled="!hasAdvancedSettings || !ask_name_submitter" />
                        @if(!$hasAdvancedSettings)
                            <x-icon name="o-lock-closed" class="text-gray-400" title="{{ __('This setting is not available with your current subscription') }}" />
                        @endif
                    </div>

                    <div class="flex items-start gap-4 sm:flex-row flex-col">
                        <x-checkbox label="{{ __('Email') }}" wire:model="ask_email_submitter" />

                        <x-checkbox label="{{ __('Email required') }}" wire:model="require_email_submitter"
                            x-bind:disabled="!hasAdvancedSettings || !ask_email_submitter" />
                        @if(!$hasAdvancedSettings)
                            <x-icon name="o-lock-closed" class="text-gray-400" title="{{ __('This setting is not available with your current subscription') }}" />
                        @endif
                    </div>
                </div>

                <x-menu-separator />
                <x-input type="number" label="{!! __('Max images per user')!!}" placeholder="{!! __('Max images per user')!!}" wire:model="max_images_submitter" max="99" inline required />

                <!-- ADVANCED USER SETTINGS -->
                <x-advanced-settings> 
                    <x-toggle label="{{__('Display user name?')}}" wire:model="submitter_name_on_wall" :disabled="!$hasAdvancedSettings" right inline />
                    <x-toggle label="{{__('Enable avatar selection and display?')}}" wire:model="require_avatar_submitter" :disabled="!$hasAdvancedSettings" right inline/>
                    
                    <div x-data="{ allow_captions: @entangle('allow_captions') }">
                        <x-toggle label="{{__('Allow captions?')}}" x-model="allow_captions" 
                            :disabled="!$hasAdvancedSettings" wire:model="allow_captions" class="mb-[10px]" right inline/>
                        <template x-if="allow_captions">
                            <div>
                                <x-input 
                                    label="{{ __('Max captions characters') }}" 
                                    placeholder="{{ __('Max captions characters') }}" 
                                    wire:model="caption_max_characters"
                                    :disabled="!$hasAdvancedSettings"
                                    type="number" 
                                    min="10" 
                                    max="255"
                                    hint="Min: 10, max: 255"
                                    inline
                                />
                            </div>
                        </template>
                    </div>

                    @php
                        $capture_mode_options = [
                            ['custom_key' => 0 , 'name' => __('Gallery')],
                            ['custom_key' => 1 , 'name' => __('Front camera')],
                            ['custom_key' => 2 , 'name' => __('Rear camera')],
                        ];
                    @endphp
                    <div class="flex justify-center text-center">
                        <x-group
                            label="{{ __('By default, the user can select and upload an image from:') }}"
                            :options="$capture_mode_options"
                            wire:model="capture_mode"
                            option-value="custom_key"
                            :disabled="!$hasAdvancedSettings"
                            class="[&:checked]:!btn-primary btn-sm normal-case" />
                    </div>
                </x-advanced-settings> 

                <x-slot:actions style="justify-content: space-between !important;" class="flex-wrap">
                    <x-button label="{{ __('Go back') }}" @click="step = Math.max(1, step - 1)" type="button" icon="o-arrow-left" class="btn" spinner="updateSubmitterSettings"/>
                    <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateSubmitterSettings" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </template>


<!---------------
----  STEP 5 : Sharing settings.
----------------->
    <template x-if="step === 5">
        <div class="flex w-full justify-center gap-6 flex-wrap text-center">
            <x-card title="{{ __('Sharing links') }}" shadow separator>
            <x-form>
                <div x-data="{ copied: false }" class="text-center">
                    <div class="flex items-center sm:items-end justify-center gap-2 sm:flex-row flex-col w-full">
                        <x-input 
                            x-bind:value="'{{ $this->displayImageUrl }}'"
                            type="text"
                            label="{!! __('Wall display link') !!}"
                            readonly 
                            class="w-full px-3 py-2 text-sm"
                        />
                        <x-button 
                            label="{{ __('Copy') }}"
                            type="button" 
                            @click="navigator.clipboard.writeText('{{ $this->displayImageUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })" 
                            icon="o-clipboard-document-check"
                        />
                    </div>
                    <p x-show="copied" x-transition class="text-green-600 text-sm mt-1">
                        {{ __('Link copied!') }}
                    </p>
                </div>

                            
                <div x-data="{ copied: false }" class="text-center">
                    <div class="flex items-center sm:items-end justify-center gap-2 sm:flex-row flex-col w-full">
                        <x-input 
                            x-bind:value="'{{ $this->CreateImageUrl }}'"
                            type="text"
                            label="{!! __('Link to post image') !!}"
                            readonly 
                            class="w-full px-3 py-2 text-sm"
                        />
                        <x-button 
                            label="{{ __('Copy') }}"
                            type="button" 
                            @click="navigator.clipboard.writeText('{{ $this->CreateImageUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })" 
                            icon="o-clipboard-document-check"
                        />
                    </div>
                    <p x-show="copied" x-transition class="text-green-600 text-sm mt-1">
                        {{ __('Link copied!') }}
                    </p>
                </div>

                <!-- Display QR Code as png
                <div class="text-center mt-4">
                    <p class="mb-2 font-semibold">{{ __('QR Code to post image') }}</p>
                    <img src="data:image/png;base64,{{ $this->createImageQrCode }}" 
                    alt="QR Code" class="mx-auto w-40 h-40 border border-gray-300 rounded" />
                </div>-->
                
                <!-- Display QR Code as svg -->
                <div class="text-center mt-2">
                    <p class="pt-0 label-text font-semibold mb-3">{{ __('QR Code to post image') }} :</p>
                    <div class="mx-auto w-full flex justify-center">
                        {!! $this->createImageQrCode !!}
                    </div>
                </div>

                <!-- QR Code Download buttons -->
                <p class="pt-0 label-text font-semibold mt-4 mb-2">{{ __('Download QR code') }}</p>
                <div class="flex flex-wrap justify-evenly">
                    <x-button
                        wire:click="downloadQrCode('png')"
                        class="btn flex items-center gap-2"
                        icon="o-arrow-down-tray"
                        label="PNG"
                        spinner="downloadQrCode"
                    />
                    <x-button
                        wire:click="downloadQrCode('svg')"
                        class="btn flex items-center gap-2"
                        icon="o-arrow-down-tray"
                        label="SVG"
                        spinner="downloadQrCode"
                    />
                    <x-button
                        wire:click="downloadQrCode('eps')"
                        class="btn flex items-center gap-2"
                        icon="o-arrow-down-tray"
                        label="EPS"
                        spinner="downloadQrCode"
                    />
                </div>
            </x-form>
            </x-card>


            <x-card title="{{ __('Sharing settings') }}" shadow separator>
            <x-form wire:submit="updateShareOptions">   
                @if(!$hasAdvancedSettings)
                    <x-input label="{{ __('Slug') }}" placeholder="{{ __('Slug') }}" :disabled="!$hasAdvancedSettings" 
                        wire:model="slug" icon="o-lock-closed"
                        hint="{!! __('This setting is not available with your current subscription') !!}" inline/>
                @else
                <x-input label="{{ __('Slug') }}" placeholder="{{ __('Slug') }}" :disabled="!$hasAdvancedSettings" 
                    wire:model="slug" icon="o-link"
                    hint="{!! __('Numbers and lower case letters only, no spaces') !!}" inline/>
                @endif
                
                <div x-data="{ position: @entangle('qr_code_position'),
                    get enabled() {
                        return this.position !== 'none'
                    },

                    toggle() {
                        if (this.enabled) {
                            this.position = 'none'
                        } else {
                            this.position = 'bottom-right'
                        }
                    }
                }" 
                class="mt-6">
                
                    <x-toggle label="{{__('Display QR Code?')}}" 
                    @click="toggle()" right inline
                    ::checked="enabled"
                    />

                    <div x-show="enabled" class="flex justify-center">
                        <div class="relative w-80 mt-4 bg-white aspect-video rounded-2xl overflow-hidden border shadow-lg">

                            {{-- Center content preview --}}
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-white px-4 py-2 rounded-xl text-sm font-medium">
                                    <img class="w-60 h-full" src="{{ asset('storage/slideshow-layout-preview/layout_caption_on_image.png') }}"/> 
                                </div>
                            </div>

                            {{-- Top Left --}}
                            <button
                                type="button"
                                @click="position = 'top-left'"
                                class="absolute top-3 left-3 w-10 h-10 rounded-lg border-2 transition-all duration-200 flex items-center justify-center"
                                :class="position === 'top-left'
                                    ? 'bg-primary border-primary scale-110 shadow-lg'
                                    : 'bg-gray-200 border-gray-200 hover:scale-105'"
                            >
                                <x-icon name="o-qr-code" class="w-8 h-8 text-black" />
                            </button>

                            {{-- Top Right --}}
                            <button
                                type="button"
                                @click="position = 'top-right'"
                                class="absolute top-3 right-3 w-10 h-10 rounded-lg border-2 transition-all duration-200 flex items-center justify-center"
                                :class="position === 'top-right'
                                    ? 'bg-primary border-primary scale-110 shadow-lg'
                                    : 'bg-gray-200 border-gray-200 hover:scale-105'"
                            >
                                <x-icon name="o-qr-code" class="w-8 h-8 text-black" />
                            </button>

                            {{-- Bottom Left --}}
                            <button
                                type="button"
                                @click="position = 'bottom-left'"
                                class="absolute bottom-3 left-3 w-10 h-10 rounded-lg border-2 transition-all duration-200 flex items-center justify-center"
                                :class="position === 'bottom-left'
                                    ? 'bg-primary border-primary scale-110 shadow-lg'
                                    : 'bg-gray-200 border-gray-200 hover:scale-105'"
                            >
                                <x-icon name="o-qr-code" class="w-8 h-8 text-black" />
                            </button>

                            {{-- Bottom Right --}}
                            <button
                                type="button"
                                @click="position = 'bottom-right'"
                                class="absolute bottom-3 right-3 w-10 h-10 rounded-lg border-2 transition-all duration-200 flex items-center justify-center"
                                :class="position === 'bottom-right'
                                    ? 'bg-primary border-primary scale-110 shadow-lg'
                                    : 'bg-gray-200 border-gray-200 hover:scale-105'"
                            >
                                <x-icon name="o-qr-code" class="w-8 h-8 text-black" />
                            </button>

                        </div>
                        
                    </div>

                    {{-- Hidden input for Livewire --}}
                    <input type="hidden" wire:model="qr_code_position">
                </div>


                <!-- ADVANCED SHARING / QR CODE SETTINGS -->
                <x-advanced-settings>

                    <div x-data="{ qr_code_color: @entangle('qr_code_color') }" class="mt-2 mb-4 flex flex-row items-end justify-between">
                        <x-input class="w-full" label="{{ __('QR Code color')}}" :disabled="!$hasAdvancedSettings" placeholder="{{ __('QR Code color')}}" x-model="qr_code_color">
                            <x-slot:prefix>Hex</x-slot:prefix>
                        </x-input>
                        
                        <input
                            type="color"
                            x-model="qr_code_color"
                            wire:model="qr_code_color"
                            @disabled(!$hasAdvancedSettings)
                            class="h-8 w-12 mb-[0.46rem] cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            title="{{ __('Choose a color') }}"
                        >
                    </div>
                    @error('qr_code_color')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror


                    <x-input type="number" label="{{ __('QR code size') }}" :disabled="!$hasAdvancedSettings" placeholder="{{ __('Qr code size') }}" wire:model="qr_code_size" inline>
                        <x-slot:prefix>
                            <span>%</span>
                        </x-slot:prefix>
                    </x-input>

                </x-advanced-settings>

                <x-slot:actions style="justify-content: space-between !important;" class="flex-wrap">
                    <x-button label="{{ __('Go back') }}" @click="step = Math.max(1, step - 1)" type="button" icon="o-arrow-left" class="btn" spinner="updateShareOptions"/>
                    <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateShareOptions" />
                </x-slot:actions>
            </x-form>
            </x-card>
        </div>
    </template>
</div>

</div>

</div>
