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
use Livewire\Attributes\Title;

new 
#[Title('Post an image')]

class extends Component {

    use WithFileUploads, Toast;

    public Wall $wall; // Stocke le Wall correspondant au token

    public $image;
    public string $caption = '';
    public ?string $visitorToken = '';

    public function mount(wall $wall)
    {
        $this->wall = $wall;
        
        // Get the cookie or generate a new token
        $this->visitorToken = request()->cookie('visitor_token');

        if (!$this->visitorToken) {
            $this->visitorToken = (string) \Str::uuid();

            // Save the token in a new cookie
            cookie()->queue(cookie('visitor_token', $this->visitorToken, 60 * 24 * 31)); // 1 month
        }
    }

    public function save()
    {

        if (is_numeric($this->wall->max_images_user) && $this->wall->max_images_user == 0) {
            $this->error(__('Posting is blocked by the admin'));
            return;
        }

        // Check if limit exists
        if (is_numeric($this->wall->max_images_user) && $this->wall->max_images_user > 0) {
            // Check if max image per user is reached
            $imageCount = Image::where('wall_id', $this->wall->id)
                ->where('permanent', true)
                ->where('visitor_token', $this->visitorToken)
                ->count();
            if ($imageCount >= $this->wall->max_images_user) {
                $this->error(__('You have reached the maximum number of images allowed'));
                return;
            }   
        }

        $data = $this->validate([
            'image' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:' . $this->wall->caption_max_characters,
        ]);
    
        // Saving full size image
        $path = $this->image->store('images', 'public');

        // Generating thumbnail
        // create new manager instance with desired driver
        $manager = new ImageManager(new Driver());

        // Generating thumbnail         
        // Save thumb         
        $image = InterventionImage::read($this->image->getRealPath())->scale(width: 500)->encode();
        Storage::disk('public')->put('thumbs/' . basename($path), $image);
        /* Older version with image manager
        $image = $manager->read($this->image->getRealPath())->scale(width: 500)->encode();
        Storage::disk('public')->put('thumbs/' . basename($path), $image);*/

        // Saving in database
        $parent = Image::create([
            'wall_id' => $this->wall->id,
            'name' => $path,
            'thumb' => 'thumbs/' . basename($path),
            'caption' => $this->caption,
            'visitor_token' => $this->visitorToken,
            'permanent' => true,
        ]);

        
        if (!$this->wall->moderation) {
            // Calculating the number of iterence of non-permanent image we need to add
            $wallImagesCount = Image::where('wall_id', $this->wall->id)->where('permanent', true)->count();
            $j = round($wallImagesCount*0.2);
            for ($k = 0; $k < $j; $k++) {
                // Saving in database
                Image::create([
                    'wall_id' => $this->wall->id,
                    'parent_id' => $parent->id,
                    'name' => $path,
                    'thumb' => 'thumbs/' . basename($path),
                    'caption' => $this->caption,
                    'status' => 1,
                    'visitor_token' => $this->visitorToken,
                    'permanent' => false,
                ]);
            }
        }

        $this->success(__('Image added successfully!'));

        $this->reset('image', 'caption');
    }

}; 
?>   

<div class="lg:h-screen flex items-center justify-center">
    <x-card class="flex items-center justify-center" title="{{ __('Post an image') }}">
        <x-form wire:submit="save"> 
            <x-file wire:model="image" label="{{__('Image')}}" hint="{{__('Only image formats allowed')}}" accept="image/png, image/jpeg"/>
            
            <x-progress wire:loading wire:target="image" class="progress-primary h-0.5" indeterminate />

            @if($image)
                <img src="{{ $image->temporaryUrl() }}" class="max-w-xs mx-auto shadow-md object-cover " />
            @endif

            @if ($wall->captions)
            <x-input label="{{__('Caption')}}" wire:model="caption" hint="Max : {{ $wall->caption_max_characters }}" maxlength="{{ $wall->caption_max_characters }}" />
            @endif

            <x-slot:actions>
                <x-button label="{{__('Send')}}" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
