@extends('layouts.user.app')
@section('content')
<div class="container-fluid py-4 position-relative" style="z-index: 10;">
    <!-- Background Atmosphere -->
    <div class="bg-orb bg-orb-1" style="width: 500px; height: 500px; background: rgba(220, 38, 38, 0.1); top: -100px; left: -100px; position: fixed; filter: blur(120px); opacity: 0.5; z-index: -1;"></div>
    <div class="bg-grid" style="position: fixed; inset: 0; background-image: linear-gradient(to right, rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 50px 50px; z-index: -1; mask-image: radial-gradient(circle at center, black 30%, transparent 85%);"></div>

    <div class="row" data-aos="fade-up">
        <div class="col-lg-10 mx-auto">
            <div class="text-center mb-5">
                <h1 class="h2 text-white outfit font-weight-bold mb-2">Educational Training Resource</h1>
                <p class="text-secondary">Official platform guidance and platform tutorials.</p>
            </div>

            <div class="glass-card-premium satin-border p-3 overflow-hidden" style="border-radius: 24px; box-shadow: 0 40px 100px rgba(0,0,0,0.5);">
                @php
                    $videoSource = site()->video;
                    $isYoutube = str_contains($videoSource, 'youtube.com') || str_contains($videoSource, 'youtu.be');
                    $isVimeo = str_contains($videoSource, 'vimeo.com');
                    
                    if($isYoutube) {
                        // Extract YouTube ID
                        if(str_contains($videoSource, 'v=')) {
                            parse_str(parse_url($videoSource, PHP_URL_QUERY), $vars);
                            $videoId = $vars['v'] ?? '';
                        } else {
                            $videoId = basename(parse_url($videoSource, PHP_URL_PATH));
                        }
                        $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                    } elseif($isVimeo) {
                        $videoId = basename(parse_url($videoSource, PHP_URL_PATH));
                        $embedUrl = "https://player.vimeo.com/video/" . $videoId;
                    } else {
                        $embedUrl = asset('storage/image/'.$videoSource);
                    }
                @endphp

                <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; background: #000;">
                    @if($isYoutube || $isVimeo)
                        <iframe src="{{ $embedUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    @else 
                        <video controls style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 12px;">
                            <source src="{{ $embedUrl }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </div>

                <div class="p-4 bg-glass-dark mt-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-primary-soft">
                            <i class="ri-play-circle-fill text-primary h3 mb-0"></i>
                        </div>
                        <div>
                            <h5 class="text-white mb-1 outfit font-weight-bold">Platform Tutorial</h5>
                            <p class="text-secondary small mb-0">V.1.0.4 • Platform Navigation & Execution Standards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background: rgba(220, 38, 38, 0.1); }
    .bg-glass-dark { background: rgba(0, 0, 0, 0.2); }
    .satin-border {
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        background-clip: padding-box !important;
    }
    .satin-border::after {
        content: ''; position: absolute; inset: -1px; border-radius: inherit; padding: 1px;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.02) 40%, rgba(255,255,255,0.12));
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude; -webkit-mask-composite: destination-out; pointer-events: none;
    }
</style>
@endsection
