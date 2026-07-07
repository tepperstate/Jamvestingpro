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
    min-height: 250px;
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
/* Vibrant Additions */
.glass-modal {
    background: linear-gradient(135deg, rgba(20, 25, 40, 0.95), rgba(15, 20, 30, 0.98)) !important;
    border: 1px solid rgba(100, 200, 255, 0.2) !important;
    box-shadow: 0 0 30px rgba(0, 150, 255, 0.15) !important;
}
.modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    background: rgba(0, 150, 255, 0.05);
}
.modal-title {
    background: linear-gradient(to right, #00d2ff, #3a7bd5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.glass-input {
    background: rgba(0, 0, 0, 0.2) !important;
    border: 1px solid rgba(0, 150, 255, 0.2) !important;
    color: #fff !important;
    transition: all 0.3s ease;
}
.glass-input:focus {
    border-color: #00d2ff !important;
    box-shadow: 0 0 15px rgba(0, 210, 255, 0.2) !important;
    background: rgba(0, 0, 0, 0.4) !important;
}
.btn-premium {
    background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
    border: none;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
    color: #fff;
}
.image-upload-box {
    border: 2px dashed rgba(0, 210, 255, 0.3) !important;
    background: rgba(0, 150, 255, 0.03) !important;
    transition: all 0.3s ease;
}
.image-upload-box:hover {
    border-color: #00d2ff !important;
    background: rgba(0, 210, 255, 0.08) !important;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold m-0" style="color:var(--text-primary) !important">Blog Management</h4>
        <button class="btn btn-premium btn-sm d-flex align-items-center gap-2" data-toggle="modal" data-target="#addBlogModal">
            <i class="ri-add-line"></i> Create Post
        </button>
    </div>

    <!-- Content Row -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="glass-card border-0 shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="blogTable" class="table text-white mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    <th class="border-0 pl-4">Identification</th>
                                    <th class="border-0">Visual Asset</th>
                                    <th class="border-0">Content Header</th>
                                    <th class="border-0">Chronology</th>
                                    <th class="border-0 text-right pr-4">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $v)
                                @if(is_object($v) && isset($v->id))
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                    <td class="align-middle pl-4">
                                        <span class="text-muted x-small">BLG-{{ str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="satin-border" style="width: 100px; height: 60px; border-radius: 10px; overflow: hidden; background: rgba(255,255,255,0.05); position: relative;">
                                            @php 
                                                $imgPath = $v->image ? (str_contains($v->image, 'http') ? $v->image : asset('storage/image/'.$v->image)) : 'https://images.unsplash.com/photo-1611974717482-4148419614ce?auto=format&fit=crop&w=800&q=80';
                                            @endphp
                                            <img src="{{ $imgPath }}" onerror="this.src='https://images.unsplash.com/photo-1611974717482-4148419614ce?auto=format&fit=crop&w=800&q=80'" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-white mb-1">{{ \Illuminate\Support\Str::limit($v->title, 40) }}</div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @php
                                                $cat = $v->category ?? 'General';
                                                $badgeColor = '#888888';
                                                $badgeBg = 'rgba(255,255,255,0.06)';
                                                if($cat === 'Course') {
                                                    $badgeColor = '#990000';
                                                    $badgeBg = 'rgba(153, 0, 0, 0.1)';
                                                } elseif($cat === 'Research') {
                                                    $badgeColor = '#ff3333';
                                                    $badgeBg = 'rgba(255, 51, 51, 0.1)';
                                                } elseif($cat === 'Webinar') {
                                                    $badgeColor = '#00e5ff';
                                                    $badgeBg = 'rgba(0, 229, 255, 0.1)';
                                                }
                                            @endphp
                                            <span class="badge small text-uppercase" style="font-size: 10px; border-radius: 4px; padding: 2px 6px; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeColor }}33; font-family: var(--mono, monospace);">
                                                {{ $cat }}
                                            </span>
                                        </div>
                                        <div class="text-muted x-small">{{ \Illuminate\Support\Str::limit(strip_tags($v->body), 60) }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="small text-white opacity-75">{{ \Carbon\Carbon::parse($v->created_at)->format('d M, Y') }}</div>
                                        <div class="text-muted x-small">{{ \Carbon\Carbon::parse($v->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td class="align-middle text-right pr-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a class="btn btn-xs glass-panel text-primary border-0" href="{{ route('edit_blog', $v->id) }}" title="Refine Content">
                                                <i class="ri-pencil-line" style="font-size: 14px;"></i>
                                            </a>
                                            <a class="btn btn-xs glass-panel text-danger border-0" href="{{ route('blog.delete', $v->id) }}" onclick="return confirm('Terminate this publication?')" title="Delete">
                                                <i class="ri-delete-bin-line" style="font-size: 14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Add Blog Modal -->
<div class="modal fade" id="addBlogModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document"> 
        <div class="modal-content glass-modal border-0" style="background: rgba(15, 20, 30, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header pb-3">
                <h5 class="modal-title font-weight-bold"><i class="ri-article-line me-2" style="color: #00d2ff;"></i> Create New Post</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-4">
                <form method="post" action="{{ route('blog.store') }}" id="blogForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase" style="color: #88c0d0; letter-spacing: 1px;">Post Title</label>
                                <input type="text" class="form-control glass-input" name="title" required placeholder="Enter an engaging title...">
                            </div>

                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase" style="color: #88c0d0; letter-spacing: 1px;">Category</label>
                                <select class="form-control glass-input" name="category" required>
                                    <option value="General" style="background:#1a2030;">General</option>
                                    <option value="Course" style="background:#1a2030;">Course</option>
                                    <option value="Research" style="background:#1a2030;">Research</option>
                                    <option value="Webinar" style="background:#1a2030;">Webinar</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase" style="color: #88c0d0; letter-spacing: 1px;">Post Content</label>
                                <!-- Quill Editor Container -->
                                <div id="quill-editor" style="height: 350px;"></div>
                                <!-- Hidden input to store HTML content for form submission -->
                                <input type="hidden" name="content" id="hiddenContent" required>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-uppercase" style="color: #88c0d0; letter-spacing: 1px;">Featured Image</label>
                                <div class="glass-card p-4 text-center image-upload-box" style="border-radius: 12px; cursor: pointer;" onclick="document.getElementById('imageUpload').click()">
                                    <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*" required onchange="previewImage(this)">
                                    <div id="imagePlaceholder">
                                        <i class="ri-image-add-line" style="font-size: 3.5rem; color: #00d2ff; text-shadow: 0 0 10px rgba(0,210,255,0.3);"></i>
                                        <p class="small mt-3 mb-0" style="color: #a3c1da;">Click to upload featured image<br><span style="font-size: 10px; opacity:0.6;">High resolution recommended</span></p>
                                    </div>
                                    <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; border-radius: 8px; display: none; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-premium w-100 py-3 mt-4 btn-lg font-weight-bold" style="letter-spacing: 1px; font-size: 16px;">
                                Publish Post <i class="ri-send-plane-fill ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

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
        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#blogTable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search posts..."
                }
            });
        }

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
            $('#blogForm').on('submit', function(e) {
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

