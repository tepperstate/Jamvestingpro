@extends('layouts.admin.app')
@section('title', 'Email Templates')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="glass-card border-0 mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="font-weight-bold mb-4" style="color:var(--text-primary) !important">Email Templates</h4>
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr style="font-size:15px">
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Subject</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:14px">
                            @foreach($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td><code style="color: #00d166;">{{ $template->slug }}</code></td>
                                    <td>{{ $template->subject }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.emails.edit', $template->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('admin.emails.preview', $template->id) }}" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
