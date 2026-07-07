@extends('layouts.user.app')

@section('title', 'Confirm Transaction')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4" data-aos="fade-up">
        <h2 class="outfit font-weight-bold">Transaction Execution</h2>
        <p class="text-secondary">Processing purchase for {{ $data->name }} via Secure Gateway</p>
    </div>

    <div class="glass-card shadow-lg mx-auto" style="max-width: 1000px; border-radius: 24px; overflow: hidden; border: 1px solid var(--glass-border);" data-aos="zoom-in">
        <div class="position-relative" style="height: 75vh; background: rgba(0,0,0,0.2);">
            <div class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 0;">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p class="text-secondary small">Initializing Secure Connection...</p>
            </div>
            <iframe src="{{ $data->url }}" style="width: 100%; height: 100%; border: none; position: relative; z-index: 1; background: transparent;" allowfullscreen></iframe>
        </div>
    </div>

    <div class="mt-4 text-center" data-aos="fade-up">
        <a href="{{ route('crypto.buy') }}" class="btn btn-link text-secondary text-decoration-none small fw-bold">
            <i class="ri-close-circle-line me-1"></i> TERMINATE SECURE CONNECTION
        </a>
    </div>
</div>
@endsection
