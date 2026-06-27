<?php

use Livewire\Component;
use App\Models\Wall;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component {

    public Wall $wall;
    public string $mode = 'prod';
    public array $displaySettings;


    public function mount(Wall $wall, string $mode = 'prod')
    {
        $this->wall = $wall;
        $this->mode = $mode;
        $this->displaySettings = $this->computeWallSettings();
    }

    private function calculateCaptionMargin (int $A): string
    {
        if ($A <= 4) {
            return "2%";
        }
        elseif ($A <= 20) {
            $B = round(1.2 * $A - 2);
            return "-{$B}%";
        }
        else {
            $B = round(0.433 * $A + 13.33);
            return "-{$B}%";
        }
    }


public function computeWallSettings(): array
{
    $caption_max_width = $this->wall->caption_max_width;
    $layout = $this->wall->layout;
    $caption_font_unit = $this->wall->caption_font_unit;
    $caption_font_size = $this->wall->caption_font_size;
    $submitter_name_font_size = $this->wall->submitter_name_font_size;
    $duration = $this->wall->duration*1000;
    $transition = $this->wall->transition;
    $caption_font_color = $this->wall->caption_font_color;
    $submitter_name_font_color = $this->wall->submitter_name_font_color;
    $caption_background_color = $this->wall->caption_background_color;
    $caption_background_opacity = $this->wall->caption_background_opacity;

    // Marges en pourcentage (à stocker en BDD comme int ou float)
    $marginTop = $this->wall->margin_top;
    $marginBottom = $this->wall->margin_bottom;
    $marginLeft = $this->wall->margin_left;
    $marginRight = $this->wall->margin_right;

    // Hauteur/largeur disponibles après marges
    $image_height = 100 - $marginTop - $marginBottom;
    $image_width  = 100 - $marginLeft - $marginRight;

    $image_container_style = "
        top: {$marginTop}%;
        bottom: {$marginBottom}%;
        left: {$marginLeft}%;
        right: {$marginRight}%;
    ";

    
    $caption_bellow_image_bottom_margin = $this->calculateCaptionMargin($this->wall->margin_bottom);

    // BACKGROUND COLOR AND OPACITY. Change the pourcentage of opacity from 0-100 to 0-255
    $caption_background_opacity = (int) round(($caption_background_opacity / 100) * 255);
    // Change from hexadecimal 2 caracters (ex: 0 => "00", 255 => "FF")
    $caption_background_opacity = strtoupper(str_pad(dechex($caption_background_opacity), 2, '0', STR_PAD_LEFT));   
    // Ad opcity after hex string.
    $caption_background = $caption_background_color . $caption_background_opacity;

    $qr_code_position = $this->wall->qr_code_position ?? 'bottom-right';
    $qr_code_position_class = match ($qr_code_position) {
        'top-left' => 'top-4 left-4',
        'top-right' => 'top-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'none' => 'hidden',
        default => 'bottom-4 right-4',
    };
    $qr_code_size = 'width:' . ($this->wall->qr_code_size ?? 12) . '%;';

    if ($qr_code_position === 'top-left' || $qr_code_position === 'bottom-left') {
        $qr_code_text_rotate_class = 'rotate-357 text-left';
    }
    elseif ($qr_code_position === 'top-right' || $qr_code_position === 'bottom-right') {
        $qr_code_text_rotate_class = 'rotate-3 text-right';
    }
    else {
        $qr_code_text_rotate_class = 'hidden';
    }

    $hex = $this->wall->qr_code_color ?? '#000000';
    [$r, $g, $b] = sscanf($hex, "#%02x%02x%02x");

    $qr_code_svg = (string) QrCode::format('svg')
        ->size(120)
        ->color($r, $g, $b)
        ->generate(route('create-image', ['wall' => $this->wall->slug]));

    // BACKGROUND = If background_choice=0 then we set the color. If background_choice=1 we set the url.
    if ($this->wall->background_choice == 0) {    
        $background = 'background: ' . $this->wall->background_color . ';';
    } else {
        $background = 'background:  no-repeat center url(\''. asset('storage/' . (str_contains($this->wall->background_image, '/') ? $this->wall->background_image : 'walls_images/background_images/' . $this->wall->background_image)) .'\'); background-size: 100% 100%;';
    }

    return [
        'caption_max_width' => $caption_max_width,
        'layout' => $layout,
        'caption_font_unit' => $caption_font_unit,
        'caption_font_size' => $caption_font_size,
        'submitter_name_font_size' => $submitter_name_font_size,
        'background' => $background,
        'duration' => $duration,
        'transition' => $transition,
        'caption_bellow_image_bottom_margin' => $caption_bellow_image_bottom_margin,
        'caption_font_color' => $caption_font_color,
        'submitter_name_font_color' => $submitter_name_font_color,
        'caption_background' => $caption_background,
        'image_container_style' => $image_container_style,
        'image_height' => $image_height,
        'image_width'  => $image_width,
        'qr_code_position_class' => $qr_code_position_class,
        'qr_code_size' => $qr_code_size,
        'qr_code_svg' => $qr_code_svg,
        'qr_code_text_rotate_class' => $qr_code_text_rotate_class,
        'qr_code_text_color' => $hex,
    ];
}

}; ?>

@push('head')
<script>
(function(){
    let wakeLock = null;

    async function requestWakeLock(){
        try {
            if (!('wakeLock' in navigator)) return;
            if (document.visibilityState !== 'visible') return; // avoid DOMException
            wakeLock = await navigator.wakeLock.request('screen');
            wakeLock.addEventListener('release', () => { wakeLock = null; console.log('Wake Lock released'); });
            console.log('Wake Lock acquired');
        } catch (err) {
            // DOMException: The requesting document is hidden.
            console.warn('Unable to acquire Wake Lock:', err);
            wakeLock = null;
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            requestWakeLock();
        } else {
            // when hidden, wake lock is released automatically
            wakeLock = null;
        }
    });

    // Try once on load; many browsers require a user gesture — also attempt on first interaction
    window.addEventListener('load', () => requestWakeLock());
    ['click', 'keydown', 'touchstart'].forEach(ev => {
        const handler = () => { requestWakeLock(); window.removeEventListener(ev, handler); };
        window.addEventListener(ev, handler, { once: true });
    });
})();
</script>
@endpush

<div x-data="{ ready: false }" x-init="ready = true" class="w-screen h-screen overflow-hidden">
    <!-- Loading spinner -->
    <div x-show="!ready" class="absolute inset-0 flex items-center justify-center z-50">
        <x-loading class="loading-ring" />
    </div>

    <!-- Slideshow appears only when ready = true -->
    <div x-show="ready" x-cloak class="relative">
        <!-- Slideshow bellow -->
        @if($mode === 'dev')
            <livewire:displaywalls.slideshow-dev :wall="$wall" :displaySettings="$displaySettings" />
        @elseif($mode === 'slow')
            <livewire:displaywalls.slideshow-slow :wall="$wall" :displaySettings="$displaySettings" />
        @elseif($mode === 'oldcaption')
            <livewire:displaywalls.slideshow-stable-classic-caption :wall="$wall" :displaySettings="$displaySettings" />
    @elseif($mode === 'preview')
            <livewire:displaywalls.slideshow-preview :wall="$wall" :displaySettings="$displaySettings" />
        @else
            <livewire:displaywalls.slideshow :wall="$wall" :displaySettings="$displaySettings" />
        @endif

<style>
    .qr_code_wrapper svg {
        width: 100%;
        height: 100%;
    }
</style>

        <div style="{{ $displaySettings['qr_code_size'] }}" class="absolute {{ $displaySettings['qr_code_position_class'] }} z-45 aspect-square">
            <div class="relative inline-block {{ $displaySettings['qr_code_text_rotate_class'] }}">
                <div style="color: {{ $displaySettings['qr_code_text_color'] }};" class="font-bold mb-3 inline-block rounded-[20px] border border-white/70 bg-white/95 px-4 py-2 text-xl uppercase tracking-wide shadow-[0_6px_0_0_rgba(15,23,42,0.15)]">
                    <span class="block leading-tight">{{ __('Scan to post your photo') }}&nbsp;⤵</span>
                </div>
            </div>
            <div class="qr_code_wrapper mt-2 rounded-xl border border-white/70 bg-white p-2 shadow-[0_6px_0_0_rgba(15,23,42,0.15)]">
                {!! $displaySettings['qr_code_svg'] !!}
            </div>
        </div>
    </div>

</div>