@extends('layouts.admin.app')
@section('title', 'Telegram Notifications')
@section('content')
<div class="container-fluid">
    <a onclick="history.back()" href="javascript:void(0)">back</a>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold m-0" style="color:var(--text-primary) !important">Telegram Bot Notifications</h4>
        <p class="text-muted mt-1">Configure bots to receive real-time alerts</p>
    </div>

    <div class="row">
        <!-- Add Bot Form -->
        <div class="col-lg-5">
            <div class="glass-card border-0 shadow p-4 mb-4">
                <h6 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i data-lucide="plus-circle" style="width:16px;vertical-align:middle"></i> Add New Bot
                </h6>
                <form method="POST" action="{{route('admin.telegram.store')}}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="font-text">Bot Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Deposit Alerts Bot" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Bot Token</label>
                        <input type="text" class="form-control" name="bot_token" placeholder="123456:ABC-DEF..." required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Chat ID / Channel</label>
                        <input type="text" class="form-control" name="chat_id" placeholder="-1001234567890" required>
                    </div>
                    <label class="font-text mb-2">Notification Events</label>
                    @foreach($eventLabels as $slug => $label)
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="notification_types[]" value="{{$slug}}" id="evt_{{$slug}}" checked>
                        <label class="form-check-label" for="evt_{{$slug}}" style="color:var(--text-secondary)">{{$label}}</label>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary btn-block mt-3">
                        <i data-lucide="send" style="width:14px;vertical-align:middle"></i> Add Bot
                    </button>
                </form>
            </div>
        </div>

        <!-- Bot List -->
        <div class="col-lg-7">
            <div class="glass-card border-0 shadow p-4 mb-4">
                <h6 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i data-lucide="list" style="width:16px;vertical-align:middle"></i> Configured Bots
                </h6>
                @forelse($configs as $config)
                <div class="glass-card p-4 mb-3 satin-border" style="background: rgba(255,255,255,0.02) !important;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="icon-box glass-panel p-2" style="background: rgba(255, 51, 51, 0.1) !important; border-radius: 12px;">
                                <i data-lucide="bot" class="text-success" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-1 text-white">{{$config->name}}</h6>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <code class="x-small opacity-50 text-white">ID: {{$config->chat_id}}</code>
                                    <span class="x-small text-muted">|</span>
                                    <small class="x-small text-muted">TOKEN: ***********{{substr($config->bot_token, -6)}}</small>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($config->notification_types ?? [] as $type)
                                        <span class="badge border-0 x-small px-2 py-1" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.8); border-radius:4px;">
                                            {{$eventLabels[$type] ?? $type}}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="badge {{ $config->is_active ? 'badge-success' : 'badge-danger' }} px-3 py-1 mb-3" style="border-radius:20px; font-size:10px; letter-spacing:1px; text-transform:uppercase;">
                                {{ $config->is_active ? 'Online' : 'Offline' }}
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{route('admin.telegram.test', $config->id)}}" class="btn btn-xs glass-panel text-success border-0" title="Trigger Test">
                                    <i data-lucide="zap" style="width:14px"></i>
                                </a>
                                <a href="{{route('admin.telegram.delete', $config->id)}}" class="btn btn-xs glass-panel text-danger border-0" onclick="return confirm('Disconnect this bot?')" title="Delete">
                                    <i data-lucide="trash-2" style="width:14px"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 opacity-50">
                    <div class="mb-4">
                        <i data-lucide="layers" style="width:64px; height:64px; color:var(--text-muted)"></i>
                    </div>
                    <h5 class="text-white font-weight-bold">No Notifiers Configured</h5>
                    <p class="text-muted small">Your neural network is currently silent. Add a bot on the left to start receiving real-time architectural telemetry.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>toastr.success("{{session('success')}}", "Success")</script>
@endif
@if(session('error'))
<script>toastr.error("{{session('error')}}", "Error")</script>
@endif
<script>if(typeof lucide!=='undefined') lucide.createIcons();</script>
@endsection
