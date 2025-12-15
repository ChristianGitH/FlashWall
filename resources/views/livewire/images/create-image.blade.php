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

new class extends Component {

    use Toast, WithFileUploads;

    public Wall $wall;
    public ?Submitter $submitter = null;

    public $image;
    public string $caption = '';
    public string $background = '';
    public ?string $name = '';
    public ?string $email = '';
    public ?string $terms = '';
    public ?string $avatar = '';
    public string $posting_page_font_link = '';
    public string $posting_page_font_style = '';
    public string $posting_page_end_title = '';
    public string $posting_page_end_text = '';
    public ?string $visitorToken = '';
    public string $currentCard = 'loading';

    public bool $req_data_status = false;
    public bool $showMessageCard = false;
    public string $messageText = '';
    public string $messageHeader = '';
    public string $terms_checkbox_display_card = '';

    public function mount(Wall $wall)
    {
        $this->wall = $wall;

        $this->initBackground();
        $this->initFont();
        $this->initEndMessage();
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
            $this->background = "background:  no-repeat center url('{$url}'); background-size: cover;";
        }
    }

    private function initFont(): void     
    {    // GOOGLE FONT URL :
        if (!$this->wall->posting_page_font) return;
            $font = $this->wall->posting_page_font;
            $this->posting_page_font_link = "https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $font) . "&display=swap";
            $this->posting_page_font_style = "font-family: {$font}, sans-serif;";
    }

    private function initEndMessage(): void
    {
        $this->posting_page_end_title = $this->wall->posting_page_end_title;
        $this->posting_page_end_text = $this->wall->posting_page_end_text;
    }

    private function initVisitorToken(): void
    {
        $this->visitorToken = request()->cookie('visitor_token') ?? (string) \Str::uuid();
        cookie()->queue(
            cookie(
                name: 'visitor_token',
                value: $this->visitorToken,
                minutes: 60 * 24 * 31,
                path: '/',
                domain: null,
                secure: app()->environment('production'),
                httpOnly: true,
                sameSite: 'Lax',
            )
        );    
    }

    private function initSubmitterData(): void
    {
        if ($submitterId = request()->cookie('submitter_id')) {
            $this->submitter = Submitter::find($submitterId);
            $this->name = $this->submitter?->name ?? '';
            $this->email = $this->submitter?->email ?? '';
            $this->avatar = $this->submitter?->avatar ?? '😊';
        } else {
        // si pas de submitter existant
        $this->avatar = '😊';
    }
    }


    // Checking which data we need to ask the user to fill
    private function getSubmitterRequirements(): void
    {
        $nameOk = !$this->wall->ask_name_submitter || !empty($this->name);
        $emailOk = !$this->wall->ask_email_submitter || !empty($this->email);
        $avatarOk = !$this->wall->require_avatar_submitter || !empty($this->avatar);

        $this->req_data_status = $nameOk && $emailOk && $avatarOk;

        // CHECKING WHERE WE HAVE TO DISPLAY THE TERMS CHECKBOX
        // If no data is asked, then we display the checkbox on the "upload" card
        if($this->wall->ask_name_submitter || $this->wall->ask_email_submitter) {
            $this->terms_checkbox_display_card = 'submitter';
        } else {
            $this->terms_checkbox_display_card = 'upload';
        }
    }


    // Computed property for capture_mode group input, General Settings card
    public function getCaptureValueProperty()
    {
        return match($this->wall->capture_mode) {
            1 => 'user',        // Front camera
            2 => 'environment', // Rear camera
            default => null,      // Gallery ou aucune capture forcée
        };
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
            'name' => ($this->wall->require_name_submitter ? 'required|' : 'nullable|') . 'string|max:15',
            'email' => ($this->wall->require_email_submitter ? 'required|' : 'nullable|') . 'email|max:150',
            'avatar' => ($this->wall->require_avatar_submitter ? 'required|' : 'nullable|') . 'string|max:4',
        ];
        // We add the Terms checkbox validation if the checkbox is displayed on the submitter card.
        if ($this->terms_checkbox_display_card === 'submitter') {
            $rules['terms'] = 'accepted';
        }

        // Validate data
        $data = $this->validate($rules);

        $data['token'] = $this->visitorToken;

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
            cookie()->queue(
                cookie(
                    name: 'submitter_id',
                    value: $this->submitter->id,
                    minutes: 60 * 24 * 31,
                    path: '/',
                    domain: null,
                    secure: app()->environment('production'),
                    httpOnly: true,
                    sameSite: 'Lax',
                )
            ); 
            $this->req_data_status = true;
        }
        $this->req_data_status = true;
        $this->updateCurrentCard();
    }




    public function uploadImage()
    {

        if (!$this->canPostImage()) return;

        $rules = [
            'image' => 'required|image|max:10240|dimensions:max_width=2100,max_height=2100',
            'caption' => 'nullable|string|max:' . $this->wall->caption_max_characters,
        ];
        // We add the Terms checkbox validation if the checkbox is displayed on the submitter card.
        if ($this->terms_checkbox_display_card === 'upload') {
            $rules['terms'] = 'accepted';
        }
    
        $data = $this->validate($rules);

        // File is already optimized on client side (converted to WebP, resized to 2048px)
        // But we add fallback processing in case client-side conversion failed
        // Convert to intervention image element
        $img = InterventionImage::read($this->image->getRealPath());
        
        // Safety check: Ensure image isn't larger than expected (fallback if JS didn't resize)
        $max = 2100;
        $width = $img->width();
        $height = $img->height();
        if ($width > $max || $height > $max) {
            $img->scale($max); // Scales down longest side to $max
        }

        // Generate WebP filename
        $webpFilename = pathinfo($this->image->hashName(), PATHINFO_FILENAME) . '.webp';

        // Check if file is already WebP, if not convert it
        $clientMimeType = $this->image->getMimeType();
        if ($clientMimeType !== 'image/webp') {
            // JS conversion failed, encode to WebP on server
            $webpContent = $img->encodeByExtension('webp', 80);
        } else {
            // File is already WebP, store it as-is
            $webpContent = $this->image->get();
        }

        // Save to database first to get the model
        $parent = Image::create([
            'wall_id' => $this->wall->id,
            'name' => $webpFilename,
            'webp_name' => $webpFilename,
            'thumb' => $webpFilename,
            'caption' => $this->caption,
            'visitor_token' => $this->visitorToken,
            'submitter_id' => $this->submitter->id ?? null,
            'submitter_name' => $this->submitter->name ?? null,
            'submitter_avatar' => $this->submitter->avatar ?? null,
            'permanent' => true,
        ]);

        // Save the WebP file
        Storage::disk('public')->put($parent->webp_full_path, $webpContent);

        // Generate and save thumbnail
        Storage::disk('public')->put($parent->thumb_full_path, $img->scale(width: 500)->encodeByExtension('webp', 80));

        $this->success(__('Image added successfully!'));
        $this->showMessage($this->posting_page_end_text, $this->posting_page_end_title);
        $this->reset('image', 'caption');
    }
    

}; 
?>

@push('head')
    <link rel="icon" type="image/png" href="{{ asset('/storage/favicon_ubi/ubi-favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('/storage/favicon_ubi/ubi-favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('/storage/favicon-ubi/ubi_favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/storage/favicon_ubi/ubi-apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('/storage/favicon_ubi/ubi-site.webmanifest') }}" />
    
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('uploadImage', () => ({
            processing: false,
            showPreview: true,

            async selectFile(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file (JPG, PNG, WebP, etc.)');
                    event.target.value = '';
                    return;
                }

                this.processing = true;
                // Hide preview immediately while processing new file
                this.showPreview = false;

                // Convert file to WEBP and resize
                const webpFile = await this.convertToWebp(file);

                // Send to Livewire
                this.$wire.upload('image', webpFile, 
                    () => {
                        // Success callback
                        this.processing = false;
                        this.showPreview = true;
                    }, 
                    () => {
                        // Error callback
                        this.processing = false;
                        this.showPreview = true;
                    }
                );
            },

            convertToWebp(file) {
                return new Promise(resolve => {
                    const img = new Image();
                    
                    img.onload = () => {
                        const maxSize = 2048;
                        const ratio = Math.min(maxSize / img.width, maxSize / img.height, 1); // Max 1 so we don't upscale smaller images
                        const newWidth = img.width * ratio;
                        const newHeight = img.height * ratio;

                        // If WebP and already small, skip conversion
                        if (file.type === 'image/webp' && ratio === 1) {
                            console.log("Image already in WebP format and within size limit");
                            resolve(file);
                            return;
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = newWidth;
                        canvas.height = newHeight;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, newWidth, newHeight);

                        const type = 'image/webp';
                        const quality = 0.8; // Between 0 and 1

                        canvas.toBlob(blob => {
                            const name = file.type === 'image/webp'
                                ? file.name
                                : file.name.replace(/\.\w+$/, '.webp');

                            resolve(new File([blob], name, { type }));
                        }, type, quality);
                    };

                    img.onerror = () => {
                        // Fallback to original file if image fails to load
                        console.error("Failed to load image, using original file");
                        resolve(file);
                    };

                    img.src = URL.createObjectURL(file);
                });
            }
        }));
    });
    </script>
@endpush


<!-- Page title -->
@if ($wall->posting_page_text)
    @section('title', $wall->posting_page_text)
@else
    @section('title', 'Post an image')
@endif

<div>

<style>
.file-input::file-selector-button {
    background-color: {{ $this->wall->posting_page_buttons_color }};
    border-color: {{ $this->wall->posting_page_buttons_color }};
    color: {{ $this->wall->posting_page_buttons_font_color }};
}
.input, .file-input, .custom_input {
    border-color : {{ $this->wall->posting_page_buttons_color }};
}
</style>

@if ($this->posting_page_font_link)
<link rel="stylesheet" href="{{ $this->posting_page_font_link }}"/>
@endif
<div x-data="{ currentCard: @entangle('currentCard') }"
    class="min-h-screen flex flex-col" style="{{ $background }}">
    <!-- Main content centered -->
    <main class="flex-1 flex flex-col items-center justify-center p-5 md:py-5 px-2 lg:px-10">
        <div x-show="currentCard === 'loading'" class="absolute inset-0 flex items-center justify-center z-50">
            <x-loading class="loading-ring" />
        </div>


        <x-card x-show="currentCard === 'submitter'" x-cloak class="max-w-full md:max-w-none px-2 md:px-5 flex items-center justify-center">
            @if($this->wall->posting_page_logo && $this->wall->posting_page_logo_visibility == 1)
                <img src="storage/posting_page_images/logos/{{ $this->wall->posting_page_logo }}" class="max-w-[70vw] sm:max-w-xs mb-2 mx-auto object-cover " />
            @endif
            @if($this->wall->posting_page_text_visibility)
                <h1 class="mb-4 text-2xl font-bold text-center" style="{{ $this->posting_page_font_style }}">{{ $this->wall->posting_page_text ?: __('Post an image') }}</h1>
            @endif

            <x-form wire:submit="saveSubmitterData" class="mt-6 max-w-full w-full"> 

                <!-- Name input display -->
                @if ($this->wall->ask_name_submitter && !$this->wall->require_name_submitter)
                    <x-input label="{{__('Name')}}" placeholder="{{__('Name')}}" wire:model="name" maxlength="15" inline />
                @elseif ($this->wall->ask_name_submitter && $this->wall->require_name_submitter)
                    <x-input label="{{__('Name')}} *" placeholder="{{__('Name')}}" wire:model="name" maxlength="15" inline required />
                @endif

                <!-- Email input display -->
                @if($this->wall->ask_email_submitter && !$this->wall->require_email_submitter)
                    <x-input label="{{__('Email')}}" placeholder="{{__('Email')}}" wire:model="email" maxlength="150" inline />
                @elseif ($this->wall->ask_email_submitter && $this->wall->require_email_submitter)
                    <x-input label="{{__('Email')}} *" placeholder="{{__('Email')}}" wire:model="email" maxlength="150" inline required />
                @endif


                <!-- Avatar selector display -->
                @if($this->wall->require_avatar_submitter)
                <!-- AVATAR SELECTOR -->
                <div
                    x-data="{ 
                        open: false,
                        selected: @entangle('avatar'), 
                        current: 'smileys',
                        categories: {
                            smileys: [
                                '😀','😁','😂','🤣','😃','😄','😅','😆','😉','😊',
                                '😋','😎','😍','😘','🥰','😗','😙','😚','🙂','🤗',
                                '🤩','🤔','🤨','😐','😑','😶','🙄','😏','😣','😥',
                                '😮','🤐','😯','😪','😫','🥱','😴','😌','😛','😜',
                                '😝','🤤','😒','😓','😔','😕','🙃','🤑','😲','☹️',
                                '🙁','😖','😞','😟','😤','😢','😭','😦','😧','😨',
                                '😩','🤯','😬','😰','😱','🥵','🥶','😳','🤪','🥸',
                                '😡','😠','😷','🤒','🤕','🤧','😇','🤓','🧐','🥳'
                            ],
                            animals: [
                                '🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯',
                                '🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🐤','🐣',
                                '🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🐛',
                                '🦋','🐌','🐞','🐜','🪲','🦂','🦟','🦗','🕷️','🕸️',
                                '🦖','🦕','🐢','🐍','🦎','🦑','🐙','🦀','🦞','🦐',
                                '🌺','🌻','🌹','🌷','🌼','🌸','💐','🍇','🍈','🫑',
                                '🍉','🍊','🍋','🍌','🍍','🥭','🥥','🥝','🍎','🍏',
                                '🍐','🍑','🍒','🍓','🫐','🥕','🌽','🥦','🥬','🥒'
                            ],
                        objects: [
                                '🍄','🍀','🌟','🔥','⚡','🎉','👑','💎','🪙','📦',
                                '🛒','💡','📌','📍','✏️','🖊️','🖋️','🖌️','🖍️','📝',
                                '📖','📚','🔒','🔑','🗝️','🛠️','⚙️','🧰','⏰','⌛',
                                '⏳','💰','💳','🎁','🎈','🎀','🎊','🏆','🥇','🥈',
                                '🥉','🏅','🎖️','🧸','🪀','📷','📹','🎥','📺','💻',
                                '🖥️','🖨️','🖱️','🖲️','📱','📲','☎️','📞','📟','📠',
                                '🕹️','🎮','🎲','🧩','🪁','🚪','🪑','🛋️','🛏️',
                                '🛁','🚿','🪞','🧴','🧹','🧺','🪣','🪄'
                            ],
                            flags: [
                                '🇦🇷','🇦🇩','🇦🇲','🇦🇺','🇦🇹','🇧🇪','🇧🇭','🇧🇾','🇧🇷','🇱🇺',
                                '🇨🇦','🇨🇱','🇨🇳','🇭🇷','🇨🇾','🇨🇿','🇩🇰','🇪🇸','🇪🇪','🇫🇮',
                                '🇫🇷','🇬🇧','🇬🇪','🇩🇪','🇬🇷','🇬🇱','🇭🇺','🇮🇳','🇮🇪','🇮🇹',
                                '🇯🇵','🇰🇿','🇰🇬','🇰🇷','🇱🇻','🇱🇮','🇱🇹','🇲🇩','🇲🇬','🇲🇰',
                                '🇲🇽','🇳🇱','🇳🇴','🇳🇿','🇵🇱','🇷🇴','🇸🇮','🇸🇷','🇨🇭','🇸🇰',
                                '🇹🇷','🇺🇦','🇺🇸','🇺🇿','🇿🇦','🇪🇬','🇵🇹','🇹🇭','🇧🇬','🇷🇸',
                                '🇻🇳','🇸🇬','🇵🇭','🇷🇺','🇨🇴','🇰🇵','🇳🇬','🇸🇪','🇪🇨','🇱🇧',
                                '🇧🇴','🇨🇷','🇹🇳','🇨🇺','🇦🇱','🇲🇾','🇦🇿','🇵🇦'
                            ]
                        }
                    }">

                    <!-- Select avatar button -->
                    <div class="flex justify-center items-center gap-2">
                        <p class="text-sm font-medium">Avatar :</p>
                        <x-button
                            type="button"
                            @click="open = !open"
                            class="emoji_font custom_input btn btn-circle btn-xl border-none bg-base-300 hover:bg-base-200 p-4">
                            <span x-text="selected || '😊'" class="inline-block text-4xl leading-none"></span>
                        </x-button>
                    </div>

                    <!-- Overlay for avatar selection -->
                    <div 
                        x-show="open" 
                        x-transition
                        class="fixed z-50 inset-0 flex items-center justify-center"
                    >

                        <div @click.outside="open = false" class="relative p-4 mx-2 max-w-md h-[50svh] bg-white dark:bg-gray-800 border rounded-lg shadow-lg">
                            <!-- Close button -->
                            <button
                                @click="open = false" 
                                type="button"
                                class="absolute top-2 right-2 text-gray-500 hover:text-gray-black focus:outline-none"
                                title="{{ __('Close') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- TABS -->
                            <div class="emoji_font tabs w-full mb-[-1px]">
                                <button
                                    type="button"
                                    class="p-2 border hover:bg-base-200 bg-base-300 rounded-t-lg" 
                                    :class="current === 'smileys' && 'tab-active bg-white dark:bg-gray-800 border-b-transparent'"
                                    @click="current = 'smileys'"
                                >😄</button>
                                <button
                                    type="button"
                                    class="p-2 border hover:bg-base-200 bg-base-300 rounded-t-lg"
                                    :class="current === 'animals' && 'tab-active bg-white dark:bg-gray-800 border-b-transparent'"
                                    @click="current = 'animals'"
                                >🐾</button>
                                <button
                                    type="button"
                                    class="p-2 border hover:bg-base-200 bg-base-300 rounded-t-lg"
                                    :class="current === 'flags' && 'tab-active bg-white dark:bg-gray-800 border-b-transparent'"
                                    @click="current = 'flags'"
                                >🏁</button>
                                <button
                                    type="button"
                                    class="p-2 border hover:bg-base-200 bg-base-300 rounded-t-lg"
                                    :class="current === 'objects' && 'tab-active bg-white dark:bg-gray-800 border-b-transparent'"
                                    @click="current = 'objects'"
                                >✨</button>
                            </div>

                            <!-- Tabs content -->
                            <div class="emoji_font border max-h-[calc(50svh-4rem)] flex flex-wrap gap-x-2 overflow-y-auto">
                                <template x-for="emoji in categories[current]" :key="emoji">
                                    <button
                                        type="button"
                                        class="hover:bg-base-200 rounded p-1 text-2xl" 
                                        @click="selected = emoji; open = false;"
                                        x-text="emoji"
                                    ></button>
                                </template>
                            </div>
                        </div>

                    </div>

                </div>
                @endif


                <!-- We add the Terms checkbox if needed, depends if some data is asked to submitter -->
                @if($this->terms_checkbox_display_card === 'submitter') 
                    <x-checkbox wire:model="terms" required>
                        <x-slot:label>
                            {{__('I agree with the')}} <a href="{{ route('terms', app()->getLocale()) }}" class="underline">{{ __('terms.title') }}</a>
                        </x-slot:label>
                    </x-checkbox>
                @endif
                
                <x-slot:actions>
                    <x-button label="{{__('Next')}}" icon="o-paper-airplane" spinner="saveSubmitterData" type="submit"
                    wire:loading.attr="disabled"
                    wire:target="saveSubmitterData"
                    wire:loading.class="opacity-50"
                    class="{{ $this->wall->posting_page_buttons_color ? '' : 'btn-primary' }}"
                    style="{{ $this->wall->posting_page_buttons_color ? '
                    border-color: ' .$this->wall->posting_page_buttons_color. '; 
                    color:' .$this->wall->posting_page_buttons_font_color. '; 
                    background-color:'.$this->wall->posting_page_buttons_color : '' }}" />
                </x-slot:actions>
            </x-form>

            <div class="w-full flex justify-center pt-0 mt-4 -mb-4">
                @include('partials.language-switcher')
            </div>
        </x-card>





        <x-card x-show="currentCard === 'upload'" x-cloak  class="max-w-full md:max-w-none px-2 md:px-5 flex items-center justify-center">
            @if($this->wall->posting_page_logo && $this->wall->posting_page_logo_visibility == 1)
                <img src="storage/posting_page_images/logos/{{ $this->wall->posting_page_logo }}" class="max-w-[70vw] sm:max-w-xs mt-1 mb-2 mx-auto object-cover " />
            @endif
            @if($this->wall->posting_page_text_visibility)
                <h1 class="mb-3 text-2xl font-bold text-center" style="{{ $this->posting_page_font_style }}">{{ $this->wall->posting_page_text ?: __('Post an image') }}</h1>
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
                <a role="button" class="flex justify-center items-center mb-3 max-w-full" @click="currentCard = 'submitter';">
                    <x-icon name="o-user-circle" title="Change" />
                    {{ $label }}
                    <x-icon name="o-pencil-square" class="text-gray-500 ml-2" title="Change" />
                </a>
            @endif


            <x-form x-data="uploadImage" wire:submit="uploadImage" class="max-w-full w-full"> 

                <input class="file-input w-full" type="file" @change="selectFile" x-ref="fileInput" accept="image/*">

                <!-- Barre de progression -->
                <x-loading x-show="processing" class="justify-self-center loading-ring" />

                @if($image)
                    <div x-show="showPreview">
                        <img src="{{ $image->temporaryUrl() }}" class="max-w-9/10 max-h-[30vh] mx-auto shadow-md object-cover" />
                    </div> 
                @endif

                @if($wall->allow_captions)
                <x-input label="{{__('Write your message here')}}" placeholder="{{__('Write your message here')}}" wire:model="caption" hint="Max : {{ $wall->caption_max_characters }}" maxlength="{{ $wall->caption_max_characters }}" inline />
                @endif
                
                <!-- We add the Terms checkbox if needed, depends if some data is asked to submitter -->
                @if($this->terms_checkbox_display_card === 'upload')
                    <x-checkbox wire:model="terms" required>
                        <x-slot:label>
                            {{__('I agree with the')}} <a href="{{ route('terms', app()->getLocale()) }}" class="underline">{{ __('terms.title') }}</a>
                        </x-slot:label>
                    </x-checkbox>
                @endif

                <x-slot:actions>
                    <x-button label="{{__('Send')}}" icon="o-paper-airplane" spinner="uploadImage, image" type="submit"
                    x-bind:disabled="processing"
                    @click="if (processing) $event.preventDefault();"
                    wire:loading.attr="disabled"
                    wire:target="image, uploadImage"
                    wire:loading.class="opacity-50"
                    x-bind:class="{ 'opacity-50': processing }"
                    class="{{ $this->wall->posting_page_buttons_color ? '' : 'btn-primary' }}"
                    style="{{ $this->wall->posting_page_buttons_color ? '
                    border-color: ' .$this->wall->posting_page_buttons_color. '; 
                    color:' .$this->wall->posting_page_buttons_font_color. '; 
                    background-color:'.$this->wall->posting_page_buttons_color : '' }}" />
                </x-slot:actions>
            </x-form>


            <div class="w-full flex justify-center pt-0 mt-4 -mb-4">
                @include('partials.language-switcher')
            </div>
        </x-card>


        <x-card x-show="currentCard === 'message'" x-cloak class="flex flex-col max-w-full md:max-w-none px-2 md:px-5 items-center justify-center">
            <h1 class="text-2xl font-bold mb-4" style="{{ $this->posting_page_font_style }}">
                {{ $messageHeader }}
            </h1>
            <p class="text-lg mb-6">{{ $messageText }}</p>
        </x-card>



    </main>

</div>
</div>