@extends('email.layout')

@section('title', $subject ?? 'Notification')

@section('content')
    @if(isset($greeting))
        <h2 style="color: #ffffff; font-size: 24px; margin-bottom: 20px;">{{ $greeting }}</h2>
    @endif

    @if(isset($body))
        <p class="text">{!! nl2br(e($body)) !!}</p>
    @endif

    @if(isset($url) && isset($data))
        <div class="button-wrapper">
            <a href="{{ $url }}" class="button">{{ $data }}</a>
        </div>
    @endif

    @if(isset($thanks))
        <p class="text" style="margin-top: 30px;">
            {{ $thanks }}
        </p>
    @endif
@endsection
