@extends('layouts.admin.app')
@section('title', 'Edit Onboarding Question')

@section('content')
<div class="container-fluid">
    <div class="glass-card mb-4">
        <div class="p-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-white mb-1">Edit Question</h4>
                <p class="text-muted small mb-0">Update the progressive disclosure question.</p>
            </div>
            <a href="{{ route('admin.onboarding.index') }}" class="btn btn-outline-light btn-sm">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card">
                <div class="p-4">
                    <form action="{{ route('admin.onboarding.update', $question->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label class="text-white-50">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $question->title }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-white-50">Subtitle (Optional)</label>
                            <input type="text" name="subtitle" class="form-control" value="{{ $question->subtitle }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-white-50">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $question->sort_order }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-white-50">Depends On (Condition logic)</label>
                                    <select name="depends_on" class="form-control bg-dark text-white">
                                        <option value="">None (Always show)</option>
                                        @foreach($allOptions as $opt)
                                            <option value="{{ $opt->value }}" {{ $question->depends_on == $opt->value ? 'selected' : '' }}>If user chose: {{ $opt->value }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Will only show if previous answer matches this value</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-white-50">Section (1: Background, 2: Financials, 3: Compliance, 4: Finalize)</label>
                                    <input type="number" name="section" class="form-control" value="{{ $question->section ?? 1 }}" min="1" max="4" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-white-50">Question Key (Unique e.g. q1_background)</label>
                                    <input type="text" name="question_key" class="form-control" value="{{ $question->question_key }}" placeholder="Unique key for this question" required>
                                </div>
                            </div>
                        </div>

                        <hr class="border-white-10 my-4">
                        <h5 class="text-white mb-3">Options (Choices)</h5>
                        
                        <div id="options-container">
                            @foreach($question->options as $index => $opt)
                            <div class="option-row mb-3 p-3 border border-white-10 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="text-white-50 small">Label</label>
                                        <input type="text" name="options[{{$index}}][label]" class="form-control" value="{{ $opt->label }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-white-50 small">Value</label>
                                        <input type="text" name="options[{{$index}}][value]" class="form-control" value="{{ $opt->value }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-white-50 small">Icon (lucide)</label>
                                        <input type="text" name="options[{{$index}}][icon]" class="form-control" value="{{ $opt->icon }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-option"><i class="ri-delete-bin-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-outline-info btn-sm mb-4" id="add-option">
                            <i class="ri-add-line"></i> Add Option
                        </button>

                        <div class="form-group mt-4 text-right">
                            <button type="submit" class="btn btn-primary px-4">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let optionIndex = {{ $question->options->count() }};
    $('#add-option').click(function() {
        let row = `
            <div class="option-row mb-3 p-3 border border-white-10 rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="text-white-50 small">Label</label>
                        <input type="text" name="options[${optionIndex}][label]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="text-white-50 small">Value</label>
                        <input type="text" name="options[${optionIndex}][value]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="text-white-50 small">Icon (lucide)</label>
                        <input type="text" name="options[${optionIndex}][icon]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-option"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>
            </div>
        `;
        $('#options-container').append(row);
        optionIndex++;
    });

    $(document).on('click', '.remove-option', function() {
        if ($('.option-row').length > 1) {
            $(this).closest('.option-row').remove();
        } else {
            alert('You must have at least one option.');
        }
    });
});
</script>
@endpush
@endsection
