@extends('layouts.user.app')
@section('title', 'My Bots')
@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-5 bot-header-section" style="opacity: 0;">
        <div class="col-xl-9">
            <h1 class="h2 outfit font-weight-bold text-white mb-2">My Bots</h1>
            <p class="text-secondary small">Deploy automated trading strategies and let AI optimize your portfolio.</p>
        </div>
        <div class="col-xl-3 text-xl-end d-flex align-items-center justify-content-xl-end mt-3 mt-xl-0">
            <a href="{{ route('bots.history') }}" class="btn btn-premium px-4 py-2" style="border-radius: 12px; font-weight: 700;">
                <i class="ri-history-line me-2"></i> Bot History
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5 bot-cards-container">
        @forelse ($data as $d)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4 bot-card-element" style="opacity: 0;">
            <div class="glass-card-premium h-100 d-flex flex-column" style="background: rgba(16, 18, 27, 0.6); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); overflow: hidden; position: relative;">
                
                <div class="p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 4px;">
                            @if($d->image && file_exists(public_path('storage/image/'.$d->image)))
                                <img style="border-radius:50%;width: 100%; height:100%; object-fit: cover;" src="{{asset('storage/image/'.$d->image)}}" alt="{{$d->name}}">
                            @else
                                <i class="ri-robot-2-line text-primary h2 mb-0"></i>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill px-3 py-1 border border-primary border-opacity-25" style="font-size: 0.7rem; letter-spacing: 0.5px;">Automated</span>
                        </div>
                    </div>

                    <h4 class="outfit font-weight-bold text-white mb-1">{{$d->name}}</h4>
                    <div class="text-success h3 font-weight-bold mb-4">${{number_format($d->amount)}} <span class="text-secondary small" style="font-size:0.8rem; font-weight: normal;">/ deploy</span></div>

                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center gap-3 mb-3 small text-secondary">
                            <i class="ri-checkbox-circle-fill text-success" style="font-size: 1.1rem;"></i>
                            <span>Daily Trades: <strong class="text-white">{{$d->day}}</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-3 mb-3 small text-secondary">
                            <i class="ri-checkbox-circle-fill text-success" style="font-size: 1.1rem;"></i>
                            <span>Min Trade: <strong class="text-white">${{number_format($d->min)}}</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-3 mb-3 small text-secondary">
                            <i class="ri-checkbox-circle-fill text-success" style="font-size: 1.1rem;"></i>
                            <span>Max Trade: <strong class="text-white">${{number_format($d->max)}}</strong></span>
                        </li>
                        <li class="d-flex align-items-center gap-3 small text-secondary">
                            <i class="ri-user-star-fill text-primary" style="font-size: 1.1rem;"></i>
                            <span>Active Users: <strong class="text-white">{{number_format($d->used)}}</strong></span>
                        </li>
                    </ul>
                </div>

                <div class="p-4 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                    <a href="{{route('bots.user-bot',$d->id)}}" class="btn btn-primary w-100 py-3 font-weight-bold shadow-lg" style="border-radius: 12px; background: linear-gradient(135deg, #3b82f6, #2563eb); border: none;">
                        Deploy Bot
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass-card p-5 text-center" style="border-radius: 20px;">
                <i class="ri-robot-2-line text-secondary" style="font-size: 4rem; opacity: 0.5;"></i>
                <h4 class="mt-3 text-secondary outfit">No AI Bots Available</h4>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Motion !== 'undefined') {
            const { animate, stagger } = Motion;
            
            // Animate Header
            animate(".bot-header-section", { y: [-30, 0], opacity: [0, 1] }, { duration: 0.8, easing: "ease-out" });

            // Staggered Cards Entrance
            animate(".bot-card-element", 
                { y: [50, 0], opacity: [0, 1] }, 
                { delay: stagger(0.1), duration: 0.6, easing: "ease-out" }
            );

            // Add subtle hover effect via Motion One
            document.querySelectorAll('.bot-card-element .glass-card-premium').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    animate(card, { scale: 1.03, borderColor: "rgba(59, 130, 246, 0.4)" }, { duration: 0.3 });
                });
                card.addEventListener('mouseleave', () => {
                    animate(card, { scale: 1, borderColor: "rgba(255, 255, 255, 0.05)" }, { duration: 0.3 });
                });
            });
        }
    });
</script>
@endpush
@endsection
