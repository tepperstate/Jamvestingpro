@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
      <!-- Page Heading -->
      <div class="d-flex align-items-center justify-content-between mb-4">
         <div class="d-flex align-items-center gap-3">
             <a onclick="history.back()" href="javascript:void(0)" class="btn btn-sm btn-dark d-flex align-items-center gap-2">
                 <i class="fa fa-arrow-left"></i> Back
             </a>
             <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Add Video</h4>
         </div>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 p-4">
               <div class="card-body p-0">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h5 class='font-weight-bold mb-3' style='color:var(--text-primary) !important'>Upload Details</h5>
                        <p class="text-muted small">Enter the title and the YouTube Video ID to add a new educational video.</p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('upload_videos')}}" id="submit" class='row g-4'>
                           @csrf
                           <div class='col-md-6'>
                              <label class='font-text'>Title</label>
                              <input type='text' class='form-control form-control-lg' name='title' id="title" placeholder="e.g. Trading Strategy 101" required>
                              @error('title')
                                 <small class="text-danger mt-1 d-block">{{$message}}</small>
                              @enderror
                           </div>
                           <div class='col-md-6'>
                              <label class='font-text'>YouTube Video ID</label>
                              <input type='text' class='form-control form-control-lg' name='link' id="link" placeholder="e.g. dQw4w9WgXcQ" required>
                              @error('upload')
                                 <small class="text-danger mt-1 d-block">{{$message}}</small>
                              @enderror
                           </div>
                           <div class="col-12 text-end mt-4">
                               <button type="submit" id="button" class='btn btn-premium px-5'>
                                   <i class="fa fa-save me-2"></i> SAVE VIDEO
                               </button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 p-4">
               <div class="card-body p-0">
                  <h5 class="mb-3 font-weight-bold" style="color:var(--text-primary) !important">All Videos</h5>
                  <div class="table-responsive">
                     <table id="table" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-dark">
                           <tr style='font-size:15px'>
                              <th width="5%">SN</th>
                              <th width="45%">Title</th>
                              <th width="30%">Video ID</th>
                              <th width="20%" class="text-end">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @forelse ($data as $key=>$value )
                              <tr style='font-size:15px'>
                                 <td>
                                    {{++$key}}
                                 </td>
                                 <td>{{$value->title}}</td>
                                 <td class="text-mono">{{$value->vidoes}}</td>
                                 <td class="text-end">
                                     <a href="{{route('edit_vidoes',$value->id)}}">edit</a> |
                                     <a href="{{route('delete_vidoes',$value->id)}}">delete</a>
                                 </td>
                              </tr>
                           @empty
                              
                           @endforelse
                          
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
     <script>
         $(document).ready(function() {
            $('#table').DataTable()
         })
     </script>
@endsection
