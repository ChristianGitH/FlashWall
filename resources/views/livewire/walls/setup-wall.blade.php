<?php

use App\Models\Wall;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
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

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $max_images_submitter;
    public bool $ask_name_submitter = false;
    public bool $require_name_submitter = false;
    public bool $ask_email_submitter = false;
    public bool $require_email_submitter = false;
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
    public int $caption_position;
    public int $caption_font_size;
    public int $margin_top;
    public int $margin_bottom;
    public int $margin_left;
    public int $margin_right;
    public string $caption_font_color;
    public string $caption_background_color;
    public int $caption_background_opacity;
    public int $caption_max_characters;

    public ?string $posting_page_text;
    public bool $posting_page_text_visibility;
    public ?string $posting_page_font;
    public ?string $posting_page_buttons_color;
    public ?string $posting_page_buttons_font_color;
    public string $posting_page_logo;
    public $new_posting_page_logo;
    public bool $posting_page_logo_visibility;
    public string $posting_page_background_color;
    public string $posting_page_background_image;
    public $new_posting_page_background_image;
    public string $posting_page_background_choice;
   
    public string $lastSavedSlug;
    public array $background_choice_options = [];
    
    public array $googleFonts = [];

    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->name = $wall->name;
        $this->slug = $wall->slug;
        if($wall->description) {
            $this->description = $wall->description;
        }
        if($wall->max_images_submitter) {
            $this->max_images_submitter = $wall->max_images_submitter;
        }
        $this->ask_name_submitter = $wall->ask_name_submitter;
        $this->require_name_submitter = $wall->require_name_submitter;
        $this->ask_email_submitter = $wall->ask_email_submitter;
        $this->require_email_submitter = $wall->require_email_submitter;
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
        $this->caption_position = $wall->caption_position;
        $this->caption_font_size = $wall->caption_font_size;
        $this->margin_top = $wall->margin_top;
        $this->margin_bottom = $wall->margin_bottom;
        $this->margin_left = $wall->margin_left;
        $this->margin_right = $wall->margin_right;
        $this->caption_font_color = $wall->caption_font_color;
        $this->caption_background_color = $wall->caption_background_color;
        $this->caption_background_opacity = $wall->caption_background_opacity;
        $this->caption_max_characters = $wall->caption_max_characters;

        // Custom style and images for create-image page
        $this->posting_page_text = $wall->posting_page_text;
        $this->posting_page_text_visibility = $wall->posting_page_text_visibility;
        $this->posting_page_font = $wall->posting_page_font;
        $this->posting_page_buttons_color = $wall->posting_page_buttons_color;
        $this->posting_page_buttons_font_color = $wall->posting_page_buttons_font_color;
        $this->posting_page_logo = $wall->posting_page_logo;
        $this->posting_page_logo_visibility = (bool) $wall->posting_page_logo_visibility;
        $this->posting_page_background_color = $wall->posting_page_background_color;
        $this->posting_page_background_image = $wall->posting_page_background_image;
        $this->posting_page_background_choice = $wall->posting_page_background_choice;

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
            $url = "https://www.googleapis.com/webfonts/v1/webfonts?key=$apiKey&sort=popularity";

            $response = @file_get_contents($url);
            if (!$response) {
                return []; // fallback vide si l'API échoue
            }

            $data = json_decode($response, true);

        // Trier par popularité et ne garder que les 100 premières
        $topFonts = collect($data['items'] ?? [])
            ->sortBy(fn($font) => $font['family']) // trie par ordre alphabétique
            ->take(100)
            ->map(fn($font) => [
                'custom_key' => $font['family'], // valeur sélectionnée
                'name' => $font['family'],       // texte affiché
                'style' => "font-family:'{$font['family']}',sans-serif;", // style inline
                'google_url' => "https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $font['family']) . "&display=swap"
            ])->toArray();

            return $topFonts;
        });
    }


    // Function to get the last saved slug for the copy to clipboard functionality.
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
        $pngData = QrCode::format('svg')->size(200)->generate($this->CreateImageUrl);
        // for png : return base64_encode($pngData);
        return $pngData;
    }
    
    public function downloadQrCode()
    {
        $png = QrCode::format('png')
            ->size(600)
            ->margin(1)
            ->generate($this->CreateImageUrl);

        return Response::streamDownload(function () use ($png) {
            echo $png;
        }, 'Flashwall_QR_Code_'.$this->lastSavedSlug.'.png', [
            'Content-Type' => 'image/png',
        ]);
    }




    public function updateWall()
    {
        $data = $this->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
            'max_images_submitter' => 'integer|max:99',
            'ask_name_submitter' => 'boolean',
            'ask_email_submitter' => 'boolean',
            'require_name_submitter' => 'boolean',
            'require_email_submitter' => 'boolean',
            'submitter_name_on_wall' => 'boolean',
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

            
            /****** FOR WALL DISPLAY WITH COPIES ******/
            /*if ($this->wall->wasChanged('moderation')) {
                // If moderation has changed in database
                $this->handleModerationChange();
            }*/
            // Refresh navigation when a wall name is updated.
            $this->dispatch('refreshNavigation');
        } else {
            $this->warning(__('No change detected!'));
        }
    }

    /****** FOR WALL DISPLAY WITH COPIES ******/
    /*protected function handleModerationChange()
    {
        if ($this->wall->moderation) {
        // Moderation has been activated
        // Delete all images copies (permanent = flase) with a parent not approved (status != 1).
            $copiesToDelete = Image::where('wall_id', $this->wall->id)
            ->where('permanent', false)
            ->whereHas('parent', function ($query) {
                $query->where('status', '!=', 1);
            })
            ->pluck('id');      

            Image::whereIn('id', $copiesToDelete)->update(['status' => 5]);

        } else {
            // Moderation has been desactivated
             // Get all parent images (permanent = true) which are not approved (status != 1) and not deleted (status != 5)
            $parents = Image::where('wall_id', $this->wall->id)
                ->where('permanent', true)
                ->whereNotIn('status', [1, 5])
                ->get();

            // Calculate the number of copies to create based on the number of parent image
            $copiesToCreate = round($parents->count() * 0.2);

            foreach ($parents as $parent) {
                for ($k = 0; $k < $copiesToCreate; $k++) {
                    Image::create([
                        'wall_id' => $this->wall->id,
                        'parent_id' => $parent->id,
                        'name' => $parent->name,
                        'thumb' => $parent->thumb,
                        'caption' => $parent->caption,
                        'permanent' => false,
                    ]);
                }
            }
        }
    }*/




    public function updatePostingPageStyle()
    {
        $data = $this->validate([
            'posting_page_text' => 'string|max:155',
            'posting_page_text_visibility' => 'required|boolean',
            'posting_page_font' => 'string|max:155',
            'posting_page_buttons_color' => ['string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'posting_page_buttons_font_color' => ['string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_posting_page_logo' => 'nullable|image|max:1024',
            'posting_page_logo_visibility' => 'required|boolean',
            'posting_page_background_color' => ['string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_posting_page_background_image' => 'nullable|image|max:20480',
            'posting_page_background_choice' => 'required|integer|max:2',
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

        if ($this->new_posting_page_background_image) {
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
        $this->wall->posting_page_text = $this->posting_page_text;
        $this->wall->posting_page_text_visibility = $this->posting_page_text_visibility;
        $this->wall->posting_page_font = $this->posting_page_font;
        $this->wall->posting_page_buttons_color = $this->posting_page_buttons_color;
        $this->wall->posting_page_buttons_font_color = $this->posting_page_buttons_font_color;
        $this->wall->posting_page_logo_visibility = $this->posting_page_logo_visibility;
        $this->wall->posting_page_background_color = $this->posting_page_background_color;
        $this->wall->posting_page_background_choice = $this->posting_page_background_choice;
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }




    public function updateShareOptions()
    {
        $data = $this->validate([
            'slug' => [
                'required',
                'string',
                'max:35',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:walls,slug,' . $this->wall->id,
            ],
        ]); 
    
        // Filtrer uniquement les champs modifiés
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->lastSavedSlug = $this->wall->slug; // Updating for the copy to clipboard functionality.
            $this->success(__('Changes saved!'));
            // Refresh navigation when a wall name is updated.
            $this->dispatch('refreshNavigation');
        } else {
            $this->warning(__('No change detected!'));
        }
    }




    public function updateWallBackground()
    {
        $data = $this->validate([
            'background_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'new_background_image' => 'nullable|image|max:20480',
            'background_choice' => 'required|integer|max:2',
        ]);

        if ($this->new_background_image) {
            // Suppression de l'ancienne image puis sauvegarde de la nouvelle image
            if ($this->wall->background_image !== 'walls_images/background_images/default_background.jpg' && $this->wall->background_image !== 'walls_images/background_images/grid_background.jpg') {
                Storage::disk('public')->delete($this->wall->background_image);
            }
            $background_image_path = $this->new_background_image->store('walls_images/background_images', 'public');
            $this->wall->background_image = basename($background_image_path);
        }
        
        // On prépare les changements sur le modèle (sauf l'image)
        // Remplit le modèle avec les données validées
        $this->wall->background_color = $this->background_color;
        $this->wall->background_choice = $this->background_choice;
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }




    public function updateWallLayout()
    {
        $data = $this->validate([
            'margin_top' => 'required|integer|max:90',
            'margin_bottom' => 'required|integer|max:90',
            'margin_left' => 'required|integer|max:90',
            'margin_right' => 'required|integer|max:90',
        ]);

        // Filtrer uniquement les champs modifiés
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }

    
    
    
    public function updateImagesDisplay()
    {
        $data = $this->validate([
            'duration' => 'required|integer|min:2|max:99',
            'transition' => 'required|string|in:none,fade,zoom',
        ]);

        // Filtrer uniquement les champs modifiés
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
        } else {
            $this->warning(__('No change detected!'));
        }
    }




    public function updateCaptionsSettings()
    {
        $data = $this->validate([
            'allow_captions' => 'boolean',
            'caption_on_wall' => 'boolean',
            'caption_max_characters' => 'integer|min:10|max:255',
            'caption_max_width' => 'required|integer|max:100',
            'caption_position' => 'required|integer|max:3',
            'caption_font_size' => 'required|integer|max:50',
            'caption_font_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'caption_background_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'caption_background_opacity' => 'required|integer|max:100',
        ]);

        // Filtrer uniquement les champs modifiés
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));
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
<div>
    <h1 class="text-2xl md:text-3xl lg:text-4xl">
        {{ __('Settings') }} : {{ $wall->name }}
    </h1>

<div class="flex mt-[10px] items-start justify-center gap-7 flex-wrap">
    <x-card title="{{ __('General settings') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWall">
            <x-input label="{{ __('Name') }}" placeholder="{{ __('Name') }}" wire:model="name" inline />
            <x-input label="{{ __('Description') }}" placeholder="{{ __('Description') }}" wire:model="description" inline />
                <x-menu-separator />
            <x-input type="number" label="{!! __('Max images per user')!!}" placeholder="{!! __('Max images per user')!!}" wire:model="max_images_submitter" max="99" inline required />

            <p class="pb-0 label label-text font-semibold">Requested user information</p>
            <div x-data="{
                ask_name_submitter: @entangle('ask_name_submitter'),
                ask_email_submitter: @entangle('ask_email_submitter'),
                require_name_submitter: @entangle('require_name_submitter'),
                require_email_submitter: @entangle('require_email_submitter'),
                init() {
                    this.$watch('ask_name_submitter', value => { if (!value) this.require_name_submitter = false })
                    this.$watch('ask_email_submitter', value => { if (!value) this.require_email_submitter = false })
                }
            }" class="space-y-2">
                <div class="flex items-center gap-4">
                    <x-checkbox label="{{__('Name')}}" wire:model="ask_name_submitter" />
                    <x-checkbox label="{{__('Name required')}}" wire:model="require_name_submitter" x-bind:disabled="!ask_name_submitter" />
                </div>

                <div class="flex items-center gap-4">
                    <x-checkbox label="{{__('Email')}}" wire:model="ask_email_submitter" />
                    <x-checkbox label="{{__('Email required')}}" wire:model="require_email_submitter" x-bind:disabled="!ask_email_submitter"  />
                </div>
            </div>

            <x-toggle label="{{__('Display user name?')}}" wire:model="submitter_name_on_wall" right inline hint="To manage how names are displayed, go to captions options"/>

                <x-menu-separator />

            <x-toggle label="{{__('Activate moderation?')}}" wire:model="moderation" right inline/>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWall" />
            </x-slot:actions>
        </x-form>
    </x-card>



    <x-card title="{{ __('Posting page style') }}" class="w-96" shadow separator>

        <x-form wire:submit="updatePostingPageStyle">

            <x-input label="{{ __('Welcome text') }}" hint="{{ __('It will also be the page title, even if the Welcome text is hidden') }}"
            placeholder="{{ __('Welcome text') }}" wire:model="posting_page_text" inline />

            <!-- CUSTOM FONT SELECTOR INPUT -->
            <div x-data="{
                open: false,
                selectedFont: @entangle('posting_page_font'),
                fonts: @js($googleFonts),
                selectFont(font) {
                    this.selectedFont = font.name;
                    this.open = false;
                },
                clearSelection() {
                    this.selectedFont = '';
                }
            }" class="relative">

                <!-- Clickable fake input -->
                <label class="fieldset-legend text-sm font-medium">{{ __('Welcome text font') }}</label>
                <div tabindex="0" @click="open = !open"
                    :class="open ? 'ring ring-primary ring-opacity-30' : ''"
                    class="flex items-center justify-between w-full input cursor-pointer"
                >
                    <span x-text="selectedFont || '{{ __('Select a font') }}'" 
                        class="truncate"
                        :class="selectedFont ? 'text-black dark:text-gray-400' : 'text-gray-500 dark:text-gray-400'"  
                        :style="`font-family: ${selectedFont}, sans-serif;`">
                    </span>
                    
                    <div class="flex"> <!-- Clear selection button and arrow -->
                        <template x-if="selectedFont">
                            <button
                                @click.stop="clearSelection()"
                                type="button"
                                class="text-gray-500 hover:text-gray-black focus:outline-none"
                                title="{{ __('Clear selection') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Dropdown menu -->
                <ul x-show="open" @click.outside="open = false"
                    class="absolute z-20 mt-1 w-full max-h-64 overflow-y-auto 
            bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
            rounded-lg shadow-lg">
                    <template x-for="font in fonts" :key="font.custom_key">
                        <li @click="selectFont(font)"
                            class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-sm"
                            :style="font.style">
                            <link rel="stylesheet" :href="font.google_url" />
                            <span x-text="font.name"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <!-- END CUSTOM FONT SELECTOR INPUT -->

            <x-toggle label="{{__('Display welcome text?')}}" wire:model="posting_page_text_visibility" right inline/>

            <hr>

                <div x-data="{ posting_page_buttons_color: @entangle('posting_page_buttons_color') }"class="flex flex-row items-end justify-evenly">
                    <x-input class="w-full" label="{{ __('Buttons color') }}" placeholder="{{ __('Buttons color') }}" x-model="posting_page_buttons_color" >
                        <x-slot:prefix>Hex</x-slot:prefix>
                    </x-input>
                    
                    <input
                        type="color"
                        x-model="posting_page_buttons_color"
                        wire:model="posting_page_buttons_color"
                        class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                        title="{{ __('Choose a color') }}"
                    >
                @error('posting_page_buttons_color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                </div>
                
                <div x-data="{ posting_page_buttons_font_color: @entangle('posting_page_buttons_font_color') }" class="flex flex-row items-end justify-evenly">
                    <x-input class="whitespace-nowrap overflow-visible" label="{{ __('Buttons font color') }}" placeholder="{{ __('Buttons font color') }}" x-model="posting_page_buttons_font_color" >
                        <x-slot:prefix>Hex</x-slot:prefix>
                    </x-input>

                    <input
                        type="color"
                        x-model="posting_page_buttons_font_color"
                        wire:model="posting_page_buttons_font_color"
                        class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                        title="{{ __('Choose a color') }}"
                    >
                @error('posting_page_buttons_font_color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                </div>
            </div>

        <x-menu-separator />


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
                        <img src="{{ asset('storage/posting_page_images/logos/' . $wall->posting_page_logo) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                    @endif
                </div>
            </div>

        <x-menu-separator />
        
        <div class="max-w-full overflow-hidden" x-data="{ posting_page_choice: @entangle('posting_page_background_choice') }">
            <div class="flex justify-center text-center mb-1">
                <x-group
                label="{{ __('Use as background') }} :"
                :options="$background_choice_options"
                wire:model="posting_page_background_choice"
                option-value="custom_key"
                class="[&:checked]:!btn-primary btn-sm" />
            </div>
        
            <div x-show="posting_page_choice == 0"  x-data="{ posting_page_background_color: @entangle('posting_page_background_color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="{!! __('Page background color')!!}" x-model="posting_page_background_color">
                    <x-slot:prefix>Hex</x-slot:prefix>
                </x-input>

                <input
                    type="color"
                    x-model="posting_page_background_color"
                    wire:model="posting_page_background_color"
                    class="h-8 w-12 mb-[0.46rem] cursor-pointer"
                    title="{{ __('Choose a color') }}"
                >

                @error('posting_page_background_color')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        
            <div x-show="posting_page_choice == 1"  class="max-w-full overflow-hidden">
                <x-file style="max-width: 100% !important" wire:model="new_posting_page_background_image" label="{!! __('Page background image') !!}" 
                    hint="{{ __('Only image formats allowed') }}"
                    accept="image/png, image/jpeg"
                />
                <x-progress wire:loading wire:target="new_posting_page_background_image" class="progress-primary h-0.5" indeterminate />
                @if($new_posting_page_background_image)
                    <img src="{{ $new_posting_page_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                @elseif($posting_page_background_image)
                    <img src="{{ asset('storage/posting_page_images/background_images/' . $wall->posting_page_background_image) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                @endif
            </div>
            
            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updatePostingPageStyle" />
            </x-slot:actions>
        </x-form>
    </x-card>



    <x-card title="{{ __('Share') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateShareOptions">
            <x-input label="{{ __('Slug') }}" placeholder="{{ __('Slug') }}" wire:model="slug" icon="o-link" hint="{!! __('Numbers and lower case letters only, no spaces') !!}" inline/>
            <x-menu-separator />
            
            <div x-data="{ copied: false }" class="text-center">
                <div class="flex items-end justify-center gap-2">
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
                <div class="flex items-end justify-center gap-2">
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
            <div class="text-center mt-4">
                <p class="pt-0 label-text font-semibold mb-3">{{ __('QR Code to post image') }} :</p>
                <div class="mx-auto w-full flex justify-center">
                    {!! $this->createImageQrCode !!}
                </div>
            </div>

            <x-button
                wire:click="downloadQrCode"
                class="btn-primary flex items-center gap-2"
                icon="o-arrow-down-tray"
                hint="png"
                label="{{ __('Download QR code') }}"
                spinner="downloadQrCode"
            />

           
            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateShareOptions" />
            </x-slot:actions>
        </x-form>

    </x-card>



    <x-card title="{{ __('Wall background') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWallBackground">

        <div class="max-w-full overflow-hidden pb-1" x-data="{ wall_background_choice: @entangle('background_choice') }">
            <div class="flex justify-center text-center mb-1">
                <x-group
                    label="{{ __('Use as background') }} :"
                    :options="$background_choice_options"
                    wire:model="background_choice"
                    option-value="custom_key"
                    class="[&:checked]:!btn-primary btn-sm" />
            </div>

            <div x-show="wall_background_choice == 0" x-data="{ background_color: @entangle('background_color') }"class="flex flex-row items-end justify-evenly">
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

            <div x-show="wall_background_choice == 1" class="max-w-full overflow-hidden">
                <x-file style="max-width: 100% !important" wire:model="new_background_image" label="{!! __('Background image') !!}" 
                    hint="{{ __('Only image formats allowed') }}"
                    accept="image/png, image/jpeg"
                />

                <x-progress wire:loading wire:target="new_background_image" class="progress-primary h-0.5" indeterminate />
                @if($new_background_image)
                    <img src="{{ $new_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                @elseif($background_image)
                    <img src="{{ asset('storage/walls_images/background_images/' . $wall->background_image) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
                @endif
            </div>
        </div>
            
            <!-- For dev and testing ! -->
            </br>
            <p style="width: 100%;">Background exemples for testing. Right click to download.</p>
            <div style="width: 100%; display: flex; justify-content: space-around;">
                <img src="{{ asset('storage/walls_images/background_images/default_background.jpg') }}" style="width: 45%; height: auto; border: 2px solid #4a00ff;" inline />
            </div>
            
            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallBackground" />
            </x-slot:actions>
        </x-form>
    </x-card>




    <x-card title="{{ __('Wall layout') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWallLayout">
            <x-input type="number" label="{{ __('Top margin') }}" placeholder="{{ __('Top margin') }}" wire:model="margin_top" inline >
                <x-slot:prefix>%</x-slot:prefix>
            </x-input>
            <x-input type="number" label="{{__('Bottom margin') }}" placeholder="{{__('Bottom margin') }}" wire:model="margin_bottom" inline >
                <x-slot:prefix>%</x-slot:prefix>
            </x-input>
            <x-input type="number" label="{{ __('Left margin') }}" placeholder="{{ __('Left margin') }}" wire:model="margin_left" inline >
                <x-slot:prefix>%</x-slot:prefix>
            </x-input>
            <x-input type="number" label="{{__('Right margin') }}" placeholder="{{__('Right margin') }}" wire:model="margin_right" inline >
                <x-slot:prefix>%</x-slot:prefix>
            </x-input>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallLayout" />
            </x-slot:actions>
            
        </x-form>
    </x-card>



    <x-card title="{{ __('Images display') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateImagesDisplay">
            <x-input type="number" label="{{ __('Time per image') }}" placeholder="{{ __('Time per image') }}" wire:model="duration" inline >
                <x-slot:prefix>In seconds</x-slot:prefix>
            </x-input>

            @php
                $transition_names = [
                    ['custom_key' => 'none' , 'name' => __('None')],
                    ['custom_key' => 'fade' , 'name' => 'Fade'],
                    ['custom_key' => 'zoom' , 'name' => 'Zoom'],
                ];
            @endphp

            <x-select label="Transition" wire:model="transition" :options="$transition_names" option-label="name" option-value="custom_key" />

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallLayout" />
            </x-slot:actions>
            
        </x-form>
    </x-card>




    <x-card title="{{ __('Captions') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateCaptionsSettings">

            <div x-data="{ allow_captions: @entangle('allow_captions') }">
                <x-toggle label="{{__('Allow captions?')}}" x-model="allow_captions" wire:model="allow_captions" class="mb-[10px]" right inline/>
                <template x-if="allow_captions">
                    <div>
                        <x-input 
                            label="{{ __('Max captions characters') }}" 
                            placeholder="{{ __('Max captions characters') }}" 
                            wire:model="caption_max_characters" 
                            type="number" 
                            min="10" 
                            max="255"
                            hint="Min: 10, max: 255"
                            inline
                        />
                        <x-toggle label="{{__('Display caption?')}}" wire:model="caption_on_wall" right inline/>
                    </div>
                </template>
            </div>

            
            <x-menu-separator />

            <x-badge value="{{ __('Captions styles also applies to names display') }}" class="badge-primary badge-soft badge-dash mb-1" />

            <x-input type="number" label="{{ __('Captions max width') }}" placeholder="{{ __('Captions max width') }}" wire:model="caption_max_width" inline >
                <x-slot:prefix>%</x-slot:prefix>
            </x-input>
            
            @php
                $options = [
                    ['custom_key' => 1 , 'name' => __('On image')],
                    ['custom_key' => 0 , 'name' => __('Bellow image')],
                ];
            @endphp
            <div class="flex justify-center text-center">
                <x-group
                    label="{{__('Captions position') }} :"
                    :options="$options"
                    wire:model="caption_position"
                    option-value="custom_key"
                    class="[&:checked]:!btn-primary btn-sm normal-case" />
            </div>



            <x-menu-separator />
            
            <x-input type="number" label="{{ __('Captions font size') }}" placeholder="{{ __('Captions font size') }}" wire:model="caption_font_size" inline >
                <x-slot:prefix>In pixels</x-slot:prefix>
            </x-input>

            <div x-data="{ caption_font_color: @entangle('caption_font_color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="{{ __('Font color')}}" placeholder="{{ __('Font color')}}" x-model="caption_font_color" />

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

            <div x-data="{ caption_background_color: @entangle('caption_background_color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="{!! __('Background color')!!}" placeholder="{!! __('Background color')!!}" x-model="caption_background_color" />

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

            <div x-data="{ opacity: @entangle('caption_background_opacity') }">
                <x-range
                wire:model.live.debounce="caption_background_opacity"
                min="0"
                max="100"
                step="10"
                label="{!! __('Opacity level') !!}"
                class="range-primary range-xs" />
                <div class="text-center">
                    <p>{{ __('Selected') }} : <span x-text="opacity + '%'"></span></p>
                </div>
            </div>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateCaptionStyle" />
            </x-slot:actions>
            
        </x-form>
    </x-card>


    <x-card title='Danger Zone' class="w-96 border border-error" shadow separator>
        <p>{{__('These actions will be irreversible!')}}</p>
        
        <x-menu-separator />
        <x-button label="{{__('Delete this wall')}}" icon="o-trash" class="btn btn-error"
            wire:click="deleteWall" 
            wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE" />

    </x-card>
    
</div>
</div>
