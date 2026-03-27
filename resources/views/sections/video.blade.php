<div class="video-box">

    <div class="video-player">
        @if(!empty($settings['video']))
        <video class="video-screen" autoplay muted loop controls playsinline preload="metadata">
            <source src="{{ Storage::disk('public')->url($settings['video']) }}" type="video/mp4">
        </video>
        @else
        <div class="video-screen video-empty">
            VIDEO PANEL
        </div>
        @endif

    </div>

</div>
