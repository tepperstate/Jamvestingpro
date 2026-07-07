@extends('layouts.admin.app')
@section('content')

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text font-weight-bold">AI Blog Manager</h1>
            <p class="text-muted mb-0">Autonomous RSS fetching and AI rewriting engine.</p>
        </div>
        <button class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" data-toggle="modal" data-target="#addSource" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
            <i data-lucide="plus-circle" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Add Source
        </button>
    </div>

    <div class="bento-grid">
        @forelse($sources as $source)
        <div class="glass-card bento-col-4 p-4 satin-border">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="badge {{ $source->active ? 'badge-success-glass' : 'badge-danger-glass' }} mb-2">
                        {{ $source->active ? 'ACTIVE' : 'INACTIVE' }}
                    </div>
                    <h4 class="text-white font-weight-bold mb-1">{{ $source->name }}</h4>
                    <a href="{{ $source->url }}" target="_blank" class="text-info small text-truncate d-block" style="max-width: 200px;">{{ $source->url }}</a>
                </div>
            </div>

            <div class="text-muted small mb-3">
                <i data-lucide="clock" style="width: 12px; display: inline-block;"></i> Sync: {{ ucfirst($source->cron_schedule) }}<br>
                <i data-lucide="file-text" style="width: 12px; display: inline-block;"></i> Limit: {{ $source->import_limit }} posts
            </div>

            <div class="d-flex gap-2 mt-3 pt-3 border-top" style="border-color: rgba(255,255,255,0.05) !important;">
                <a href="{{ route('admin.blog_manager.sync', $source->id) }}" class="btn btn-sm glass-panel text-success border-0 flex-grow-1">
                    <i data-lucide="refresh-cw" style="width:14px; display: inline-block; vertical-align: middle;"></i> Force Sync
                </a>
                <a href="{{ route('admin.blog_manager.delete', $source->id) }}" class="btn btn-sm glass-panel text-danger border-0" onclick="return confirm('Delete this feed source?')">
                    <i data-lucide="trash-2" style="width:14px"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="glass-panel p-5 satin-border d-inline-block">
                <i data-lucide="rss" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                <h4 class="text-white">No Feed Sources</h4>
                <p class="text-muted mb-0">Add your first RSS feed source to automate content generation.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('modals')
<div class="modal fade" id="addSource" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal border-0 satin-border shadow-2xl">
            <div class="modal-header border-bottom-0 pb-0">
                <h4 class="modal-title font-weight-bold text-white">Add Feed Source</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.blog_manager.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Source Name</label>
                            <input type="text" name="name" class="form-control glass-panel text-white border-0" required placeholder="e.g. Cointelegraph News">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">RSS URL</label>
                            <input type="url" name="url" class="form-control glass-panel text-white border-0" required placeholder="https://...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Cron Schedule</label>
                            <select name="cron_schedule" class="form-control glass-panel text-white border-0">
                                <option value="5_minutes">Every 5 Minutes</option>
                                <option value="10_minutes">Every 10 Minutes</option>
                                <option value="15_minutes">Every 15 Minutes</option>
                                <option value="30_minutes">Every 30 Minutes</option>
                                <option value="45_minutes">Every 45 Minutes</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Category</label>
                            <input list="categoriesList" name="category" class="form-control glass-panel text-white border-0" placeholder="Auto (or select/type)">
                            <datalist id="categoriesList">
                                <option value="Auto">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">AI Provider</label>
                            <select name="ai_provider" class="form-control glass-panel text-white border-0">
                                <option value="round_robin">Round Robin (Random)</option>
                                <option value="gemini">Google Gemini</option>
                                <option value="groq">Groq</option>
                                <option value="openrouter">OpenRouter</option>
                                <option value="copilot">GitHub Copilot</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Import Limit</label>
                            <input type="number" name="import_limit" class="form-control glass-panel text-white border-0" value="5">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">AI Model (Optional)</label>
                            <input type="text" name="ai_model" class="form-control glass-panel text-white border-0" placeholder="e.g. llama3-70b-8192">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">AI Paraphrase Prompt (Optional)</label>
                            <textarea name="ai_prompt" class="form-control glass-panel text-white border-0" rows="3" placeholder="e.g. Rewrite this in the style of a financial analyst."></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Translation Language (Optional)</label>
                            <input type="text" name="translation_lang" class="form-control glass-panel text-white border-0" placeholder="e.g. Spanish, French, German">
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <button type="submit" class="btn btn-premium w-100 py-3" style="background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%); border: none; color: #fff; box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3); font-weight: bold; font-size: 16px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 210, 255, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 210, 255, 0.3)'">Add Source</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@if(session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.success("{{ session('status') }}");
    });
</script>
@endif

<script>
$(document).ready(function(){
    lucide.createIcons();
});
</script>
@endsection


