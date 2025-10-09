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

use SimpleSoftwareIO\QrCode\Facades\QrCode;

new
#[Title('Settings')]

class extends Component {
    use Toast, WithFileUploads;
    
    public Wall $wall;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $max_images_user;
    public bool $captions = false;
    public bool $moderation = false;
    public string $background_color;
    public int $background_choice;
    public string $background_image;
    public $new_background_image;
    public int $duration;
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
   
    public string $lastSavedSlug;


    public function mount(Wall $wall)
    {
        $this->wall = $wall;
        $this->name = $wall->name;
        $this->slug = $wall->slug;
        if($wall->description) {
            $this->description = $wall->description;
        }
        if($wall->max_images_user) {
            $this->max_images_user = $wall->max_images_user;
        }
        $this->captions = $wall->captions;
        $this->moderation = $wall->moderation;
        $this->duration = $wall->duration;
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

        // Var for the Sharing card, copy to clipboard. 
        $this->lastSavedSlug = $wall->slug;
    }


    // Function to get the last saved slug for the copy to clipboard functionality.
    public function getDisplayImageUrlProperty(): string
    {
        return route('display-images', ['wall' => $this->lastSavedSlug]);
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


    public function updateWall()
    {
        $data = $this->validate([
            'name' => 'required|string|max:30',
            'description' => 'nullable|string|max:100',
            'max_images_user' => 'nullable|integer|max:99',
            'captions' => 'boolean',
            'moderation' => 'boolean',
            'duration' => 'required|integer|max:99',
            'caption_max_characters' => 'integer|min:10|max:255',
        ]); 
    
        // Filtrer uniquement les champs modifiés
        // Old code before using fill : $changes = array_filter($data, fn ($value, $key) => $this->wall->$key !== $value, ARRAY_FILTER_USE_BOTH);
        // Remplit le modèle avec les données validées
        $this->wall->fill($data);
    
        // Vérifie s'il y a des modifications
        if ($this->wall->isDirty()) {
            $this->wall->save();
            $this->success(__('Changes saved!'));

            if ($this->wall->wasChanged('moderation')) {
                // If moderation has changed in database
                $this->handleModerationChange();
            }
            // Refresh navigation when a wall name is updated.
            $this->dispatch('refreshNavigation');
        } else {
            $this->warning(__('No change detected!'));
        }
    }


    protected function handleModerationChange()
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
            if ($this->wall->background_image !== 'background_images/default_background.jpg' && $this->wall->background_image !== 'background_images/grid_background.jpg') {
                Storage::disk('public')->delete($this->wall->background_image);
            }
            $background_image_path = $this->new_background_image->store('background_images', 'public');
            $this->wall->background_image = $background_image_path;
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



    public function updateCaptionStyle()
    {
        $data = $this->validate([
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



    public function updateWallLayout()
    {
        $data = $this->validate([
            'caption_max_width' => 'required|integer|max:100',
            'caption_position' => 'required|integer|max:3',
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
            <x-input label="{{ __('Name') }}" wire:model="name" inline />
            <x-input label="{{ __('Description') }}" wire:model="description" inline />
            <x-menu-separator />
            <x-input type="number" label="{!! __('Max images per user')!!}" wire:model="max_images_user" inline />
            <x-input type="number" label="{{ __('Time per image') }}" wire:model="duration" hint="{{ __('In seconds') }}" inline />
            
            <div x-data="{ captions: @entangle('captions') }">
                <x-toggle label="{{__('Allow captions?')}}" x-model="captions" wire:model="captions" class="mb-[10px]" right inline/>
                <template x-if="captions">
                    <x-input 
                        label="{{ __('Max captions characters') }}" 
                        wire:model="caption_max_characters" 
                        type="number" 
                        min="10" 
                        max="255"
                        hint="Min: 10, max: 255"
                        inline
                    />
                </template>
            </div>
            <x-toggle label="{{__('Activate moderation?')}}" wire:model="moderation" right inline/>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWall" />
            </x-slot:actions>
        </x-form>
    </x-card>




    <x-card title="{{ __('Share') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateShareOptions">
            <x-input label="{{ __('Slug') }}" wire:model="slug" icon="o-link" hint="{!! __('Numbers and lower case letters only, no spaces') !!}" inline/>
            <x-menu-separator />
            
            <div x-data="{ copied: false }" class="text-center">
                <div class="flex items-end justify-center gap-2">
                    <x-input 
                        x-bind:value="'{{ $this->displayImageUrl }}'"
                        type="text"
                        label="{!! __('Wall display link') !!}"
                        readonly 
                        class="w-full px-3 py-2 border rounded text-sm"
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
                        class="w-full px-3 py-2 border rounded text-sm"
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
           
            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateShareOptions" />
            </x-slot:actions>
        </x-form>

    </x-card>



    <x-card title="{{ __('Background') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWallBackground">
            <div x-data="{ background_color: @entangle('background_color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="{!! __('Background color')!!}" x-model="background_color" />

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

            <div class="max-w-full overflow-hidden">
                <x-file style="max-width: 100% !important" wire:model="new_background_image" label="{!! __('Background image') !!}" 
                    hint="{{ __('Only image formats allowed') }}"
                    accept="image/png, image/jpeg"
                />
            </div>
            <x-progress wire:loading wire:target="new_background_image" class="progress-primary h-0.5" indeterminate />
            @if($new_background_image)
                <img src="{{ $new_background_image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
            @elseif($background_image)
                <img src="{{ asset('storage/' . $wall->background_image) }}" class="max-w-xs mx-auto shadow-md object-cover" inline />
            @endif

            @php
                $options = [
                    ['custom_key' => 1 , 'name' => 'Image'],
                    ['custom_key' => 0 , 'name' => __('Color')],
                ];
            @endphp
            <div class="flex justify-center">
                <x-radio label="{{ __('Use as background') }} :" wire:model="background_choice" :options="$options" option-value="custom_key" inline center />
            </div>
            
            <!-- For dev and testing ! -->
            </br>
            <p style="width: 100%;">Background exemples for testing. Right click to download.</p>
            <div style="width: 100%; display: flex; justify-content: space-around;">
                <img src="{{ asset('storage/background_images/grid_background.jpg') }}" style="width: 45%; height: auto; border: 2px solid #4a00ff;" inline />
                <img src="{{ asset('storage/background_images/default_background.jpg') }}" style="width: 45%; height: auto; border: 2px solid #4a00ff;" inline />
            </div>
            
            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallBackground" />
            </x-slot:actions>
        </x-form>
    </x-card>



    <x-card title="{{ __('Captions style') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateCaptionStyle">

            <x-input type="number" label="{{ __('Captions font size') }}" wire:model="caption_font_size" hint="{{ __('In pixels') }}" inline />

            <div x-data="{ caption_font_color: @entangle('caption_font_color') }"class="flex flex-row items-end justify-evenly">
                <x-input class="w-full" label="{{ __('Font color')}}" x-model="caption_font_color" />

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
                <x-input class="w-full" label="{!! __('Background color')!!}" x-model="caption_background_color" />

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



    <x-card title="{{ __('Layout') }}" class="w-96" shadow separator>

        <x-form wire:submit="updateWallLayout">
            <x-input type="number" label="{{ __('Top margin') }}" wire:model="margin_top" hint="{{ __('As a percentage') }}" inline />
            <x-input type="number" label="{{__('Bottom margin') }}" wire:model="margin_bottom" hint="{{ __('As a percentage') }}" inline />
            <x-input type="number" label="{{ __('Left margin') }}" wire:model="margin_left" hint="{{ __('As a percentage') }}" inline />
            <x-input type="number" label="{{__('Right margin') }}" wire:model="margin_right" hint="{{ __('As a percentage') }}" inline />
            <x-menu-separator />
            <x-input type="number" label="{{ __('Captions max width') }}" wire:model="caption_max_width" hint="{{ __('As a percentage') }}" inline />

            @php
                $options = [
                    ['custom_key' => 1 , 'name' => __('On image')],
                    ['custom_key' => 0 , 'name' => __('Bellow image')],
                ];
            @endphp
            <div class="flex justify-center text-center">
                <x-radio label="{{__('Captions position') }} :" class="normal-case" wire:model="caption_position" :options="$options" option-value="custom_key" inline center />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('Update') }}" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="updateWallLayout" />
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