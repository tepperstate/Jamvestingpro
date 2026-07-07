@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Chinese Deposit</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-12'>
                        <div class="glass-card border-0 mb-4 mb-4">
                           <div class="card-header border-color py-3">
                              <h6 class="m-0 font-weight-bold" style='color:white;'>Chinese Deposit</h6>
                           </div>
                           <div class="card-body">
                              <div class='row'>
                                 <div class='col-lg-3'>
                                    <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Chinese Deposit</h4>
                                 </div>
                                 <div class='col-lg-9'>
                                    <form method='post' action="{{route('update_chiness_deposit')}}" id="submit" class='row'>
                                       @csrf
                                       <div class='col-lg-12 mt-1'>
                                          <label class='font-text'>Content</label>
                                          <textarea class='form-control' id="summernote" type="text" name="data">{{$data->data}}</textarea>
                                       </div>
                                       <button type="submit" class='background button-btn' style='color:#fff'>SAVE</button>
                                    </form>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class='row'>
                     <div class='col-lg-12'>
                        <div class="glass-card border-0 mb-4 mb-4">
                           <div class="card-header border-color py-3">
                              <h6 class="m-0 font-weight-bold" style='color:white;'>Chinese Deposit</h6>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                       <tr style='font-size:15px'>
                                          <th>ID</th>
                                          <th>User</th>
                                          <th>Name</th>
                                          <th>Location</th>
                                          <th>Amount</th>
                                          <th>Whatapp</th>
                                          <th>Telegram</th>
                                          <th>Card Type</th>
                                          <th>Front </th>
                                          <th>Back</th>
                                          <th>Utility Bill</th>
                                          <th>Submission Date</th>
                                       </tr>
                                    </thead>
                                    <tbody style='font-size:14px'>
                                       @forelse($deposit as $key => $d)
                                          <tr>
                                             <td>{{++$key}}</td>
                                             <td><a class="btn btn-primary btn-sm" href="{{route('admin.user.single',$d->user_id)}}">view</a></td>
                                             <td>{{$d->name}}</td>
                                             <td>{{$d->location}}</td>
                                             <td>{{number_format($d->amount)}}</td>
                                             <td>{{$d->whatsapp}}</td>
                                             <td>{{$d->telegram}}</td>
                                             <td>{{$d->verification_type}}</td>
                                             <td><img class='img-fluid' src="{{asset('storage/image/'.$d->id_front)}}" style='height:50px;max-height:50px;'></td>  
                                             <td><img class='img-fluid' src="{{asset('storage/image/'.$d->id_back)}}" style='height:50px;max-height:50px;'></td> 
                                             <td><img class='img-fluid' src="{{asset('storage/image/'.$d->bill)}}" style='height:50px;max-height:50px;'></td> 
                                             <td>{{$d->created_at}}</td> 
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
      $('#summernote').summernote({
         placeholder: 'enter assets description',
         tabsize: 2,
         height: 200,
         toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });
   });
</script>
@endsection
