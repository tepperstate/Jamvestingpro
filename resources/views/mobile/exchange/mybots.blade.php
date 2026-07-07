@extends('layouts.user.app')
@section('title', 'My Bots')
@section('content')

<style>
.mobile-mybots-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.bot-card-mobile {
    background: rgba(16, 18, 27, 0.7);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 215, 0, 0.15); /* Gold accent */
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    position: relative;
    overflow: hidden;
}
/* Subtle gold glow behind card */
.bot-card-mobile::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 100px;
    height: 100px;
    background: rgba(255, 215, 0, 0.2);
    filter: blur(40px);
    border-radius: 50%;
    z-index: 0;
}
.bot-card-content {
    position: relative;
    z-index: 1;
}
.gold-text {
    color: #ffd700;
}
.bot-icon-wrapper {
    width: 50px;
    height: 50px;
    background: rgba(255, 215, 0, 0.1);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bot-icon-wrapper img {
    border-radius: 50%;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.bot-icon-wrapper i {
    color: #ffd700;
    font-size: 1.5rem;
}
.badge-auto {
    background: rgba(255, 215, 0, 0.1);
    color: #ffd700;
    border: 1px solid rgba(255, 215, 0, 0.3);
    font-size: 0.65rem;
    padding: 4px 10px;
    border-radius: 12px;
}
.btn-deploy-mobile {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000;
    font-weight: bold;
    border: none;
    border-radius: 12px;
    padding: 12px;
    width: 100%;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}
</style>

<div class="mobile-mybots-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-white font-weight-bold" style="font-family: 'Outfit', sans-serif;">My Bots</h4>
            <div class="small text-secondary">Automated Trading AI</div>
        </div>
        <a href="{{ route('bots.history') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
            <i class="ri-history-line me-1"></i> History
        </a>
    </div>

    <div class="row">
        @forelse ($data as $d)
        <div class="col-12">
            <div class="bot-card-mobile">
                <div class="bot-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bot-icon-wrapper">
                            @if($d->image && file_exists(public_path('storage/image/'.$d->image)))
                                <img src="{{asset('storage/image/'.$d->image)}}" alt="{{$d->name}}">
                            @else
                                <i class="ri-robot-2-line"></i>
                            @endif
                        </div>
                        <span class="badge-auto">AUTO</span>
                    </div>

                    <h5 class="text-white font-weight-bold mb-1">{{$d->name}}</h5>
                    <div class="text-success h4 font-weight-bold mb-3">${{number_format($d->amount)}} <span class="text-secondary small font-weight-normal" style="font-size:0.75rem;">/ deploy</span></div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <div class="w-100 d-flex justify-content-between text-secondary small">
                            <span><i class="ri-checkbox-circle-fill text-success me-1"></i> Daily Trades</span>
                            <strong class="text-white">{{$d->day}}</strong>
                        </div>
                        <div class="w-100 d-flex justify-content-between text-secondary small">
                            <span><i class="ri-checkbox-circle-fill text-success me-1"></i> Min Trade</span>
                            <strong class="text-white">${{number_format($d->min)}}</strong>
                        </div>
                        <div class="w-100 d-flex justify-content-between text-secondary small">
                            <span><i class="ri-checkbox-circle-fill text-success me-1"></i> Max Trade</span>
                            <strong class="text-white">${{number_format($d->max)}}</strong>
                        </div>
                        <div class="w-100 d-flex justify-content-between text-secondary small">
                            <span><i class="ri-user-star-fill gold-text me-1"></i> Active Users</span>
                            <strong class="text-white">{{number_format($d->used)}}</strong>
                        </div>
                    </div>

                    <a href="{{route('bots.user-bot',$d->id)}}" class="btn btn-deploy-mobile d-block text-center">
                        Deploy Bot
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5" style="background: rgba(255,255,255,0.02); border-radius: 16px; border: 1px dashed rgba(255,255,255,0.1);">
                <i class="ri-robot-2-line text-secondary" style="font-size: 3rem;"></i>
                <h6 class="mt-3 text-white">No AI Bots Available</h6>
                <p class="text-secondary small px-3">Check back later for automated trading strategies.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
