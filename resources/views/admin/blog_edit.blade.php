@extends('layouts.admin.app')
@section('content')
<style>
/* Quill Dark Theme Overrides */
.ql-toolbar.ql-snow {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(15, 20, 30, 0.95);
    border-radius: 8px 8px 0 0;
}
.ql-container.ql-snow {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-top: none;
    border-radius: 0 0 8px 8px;
    background: rgba(15, 20, 30, 0.5);
    color: var(--text-primary);
    min-height: 400px;
}
.ql-snow .ql-stroke {
    stroke: var(--text-secondary);
}
.ql-snow .ql-fill {
    fill: var(--text-secondary);
}
.ql-snow .ql-picker {
    color: var(--text-secondary);
}
.ql-editor {
    font-size: 15px;
    line-height: 1.6;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill px-3">
                <i class="ri-arrow-left-line me-1"></i> Back to Blogs
            </a>
            <h4 class="font-weight-bold m-0 text-white">Edit Post: <span class="text-primary">{{ $data->title }}</span></h4>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="glass-card border-0 shadow">
                <div class="card-body p-4">
                    <form method="post" action="{{ route('edit_store') }}" id="blogEditForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group mb-4">
                                    <label class="text-secondary small font-weight-bold text-uppercase">Post Title</label>
                                    <input type="text" class="form-control glass-input" name="title" value="{{ $data->title }}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-secondary small font-weight-bold text-uppercase">Category</label>
                                    <select class="form-control glass-input text-white" name="category" required style="background: rgba(15, 20, 30, 0.95); border: 1px solid rgba(255,255,255,0.1);">
                                        <option value="General" {{ ($data->category ?? 'General') == 'General' ? 'selected' : '' }}>General</option>
                                        <option value="Course" {{ ($data->category ?? 'General') == 'Course' ? 'selected' : '' }}>Course</option>
                                        <option value="Research" {{ ($data->category ?? 'General') == 'Research' ? 'selected' : '' }}>Research</option>
                                        <option value="Webinar" {{ ($data->category ?? 'General') == 'Webinar' ? 'selected' : '' }}>Webinar</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label class="text-secondary small font-weight-bold text-uppercase">Post Content</label>
                                    <!-- Quill Editor Container -->
                                    <div id="quill-editor">{!! $data->body !!}</div>
                                    <!-- Hidden input to store HTML content for form submission -->
                                    <input type="hidden" name="content" id="hiddenContent" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="form-group mb-4">
                                    <label class="text-secondary small font-weight-bold text-uppercase">Featured Image</label>
                                    <div class="glass-card p-3 text-center mb-3" style="border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px; cursor: pointer;" onclick="document.getElementById('imageUpload').click()">
                                        <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                        
                                        @php 
                                            $imgPath = $data->image ? (str_contains($data->image, 'http') ? $data->image : asset('storage/image/'.$data->image)) : 'https://images.unsplash.com/photo-1611974717482-4148419614ce?auto=format&fit=crop&w=1200&q=80';
                                        @endphp
                                        <img id="imagePreview" src="{{ $imgPath }}" onerror="this.src='https://images.unsplash.com/photo-1611974717482-4148419614ce?auto=format&fit=crop&w=1200&q=80'" alt="Preview" style="max-width: 100%; border-radius: 8px; {{ $data->image ? '' : 'display:none;' }}">
                                        
                                        <div id="imagePlaceholder" style="{{ $data->image ? 'display: none;' : '' }}">
                                            <i class="ri-image-add-line text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-secondary small mt-2 mb-0">Click to change featured image</p>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block text-center">Leave blank to keep the current image.</small>
                                </div>

                                <button type="submit" class="btn btn-premium w-100 py-3 mt-4 btn-lg font-weight-bold">
                                    Update Post <i class="ri-save-3-fill ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Quill Stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Include Quill Library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

@push('scripts')
<script>
    // Image Preview Logic
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePlaceholder').style.display = 'none';
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // Initialize Quill Editor
        if (typeof Quill !== 'undefined') {
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Write your article content here...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            // Sync Quill HTML content to hidden input before submit
            $('#blogEditForm').on('submit', function(e) {
                var html = quill.root.innerHTML;
                if(html === '<p><br></p>' || html === '') {
                    e.preventDefault();
                    toastr.error('Content cannot be empty', 'Error');
                    return false;
                }
                $('#hiddenContent').val(html);
            });
        }

        @if(session('status'))
            toastr.success("{{ session('status') }}", "Success");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}", "Error");
        @endif
    });
</script>
@endpush
@endsection
