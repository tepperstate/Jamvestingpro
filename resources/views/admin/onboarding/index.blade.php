@extends('layouts.admin.app')
@section('title', 'Onboarding Management')

@section('content')
<div class="container-fluid">
    <div class="glass-card mb-4">
        <div class="p-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-white mb-1">Onboarding Wizard (Soft Gate)</h4>
                <p class="text-muted small mb-0">Manage the dynamic progressive disclosure wizard.</p>
            </div>
            <a href="{{ route('admin.onboarding.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="mr-1" style="width:16px; display:inline-block;"></i> Add Question
            </a>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-hover text-white mb-0">
                <thead class="bg-white-05">
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Section / Key</th>
                        <th class="px-4 py-3">Depends On</th>
                        <th class="px-4 py-3">Options</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $q)
                    <tr class="border-bottom border-white-05">
                        <td class="px-4 py-3">{{ $q->sort_order }}</td>
                        <td class="px-4 py-3">
                            <div class="font-weight-bold">{{ $q->title }}</div>
                            <div class="x-small text-muted">{{ $q->subtitle }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div><span class="badge badge-info-glass">Sec {{ $q->section ?? 1 }}</span></div>
                            <div class="x-small text-muted mt-1">{{ $q->question_key }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($q->depends_on)
                                <span class="badge badge-warning-glass">{{ $q->depends_on }}</span>
                            @else
                                <span class="badge badge-secondary-glass">None (Root)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            {{ $q->options->count() }} options
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.onboarding.edit', $q->id) }}" class="btn btn-sm btn-info-glass">Edit</a>
                                <a href="{{ route('admin.onboarding.delete', $q->id) }}" class="btn btn-sm btn-danger-glass" onclick="return confirm('Delete this question and all its options?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
