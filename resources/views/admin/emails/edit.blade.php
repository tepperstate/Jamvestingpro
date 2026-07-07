@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card bg-vortex glass-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Edit Template: {{ $template->name }}</h4>
                <div class="logo-preview">
                    <img src="{{ asset('assets/img/favicon.svg') }}" alt="Logo" style="max-height: 40px;">
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.emails.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ $template->subject }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">HTML Content</label>
                        <p class="text-muted small mb-2">You can paste your HTML here. Use the variables from the sidebar.</p>
                        <textarea name="content" id="template_editor" class="form-control" rows="20">{{ $template->content }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-vortex glass-card mb-4">
            <div class="card-header">
                <h5 class="card-title">Available Variables</h5>
            </div>
            <div class="card-body">
                <div class="variable-list">
                    @if($template->variables)
                        @foreach($template->variables as $var => $desc)
                            <div class="mb-3">
                                <code class="d-block mb-1" style="font-size: 1.1rem; color: #00d166;">@{{ {{ $var }} }}</code>
                                <small class="text-muted">{{ $desc }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Common variables:</p>
                        <div class="mb-2"><code>@{{ {{ site()->name }} }}</code></div>
                        <div class="mb-2"><code>@{{ {{ site()->email }} }}</code></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card bg-vortex glass-card">
            <div class="card-header">
                <h5 class="card-title">Dynamic Website Code</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Copy-paste these standard tags into your HTML for consistent branding:</p>
                <div class="mb-2">
                    <label class="small">Site Name:</label>
                    <input type="text" readonly class="form-control form-control-sm" value="@{{ {{ site()->name }} }}">
                </div>
                <div class="mb-2">
                    <label class="small">Site Email:</label>
                    <input type="text" readonly class="form-control form-control-sm" value="@{{ {{ site()->email }} }}">
                </div>
                <div class="mb-2">
                    <label class="small">Website URL:</label>
                    <input type="text" readonly class="form-control form-control-sm" value="@{{ {{ env('APP_URL') }} }}">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(13, 20, 33, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 15px;
    }
    .form-label { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }
    .form-control { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #fff; }
    .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; }
</style>
@endsection
