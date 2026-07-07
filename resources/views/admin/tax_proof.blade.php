@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Tax Clearance Proof</h4>
      </div>
      @forelse ($data as $key => $value )
      @empty
      @endforelse
      <!-- Content Row -->
       <div class='row'>
            <div class='col-lg-12'>
                <div class="glass-card border-0 mb-4 mb-4">
                    <div class="card-header border-color py-3">
                        <h6 class="m-0 font-weight-bold" style='color:white;'>Tax Clearance Proof</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr style='font-size:15px'>
                                    <th>ID</th>
                                    <th>Name</th>

                                    <th>Tax Proof One</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                    <th>Tax Proof Two</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            @forelse ($data as $key => $value )
                                <tbody style='font-size:14px'>
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>
                                            <img class='img-fluid' download src="{{asset('storage/image/'.$value->proof_one)}}" style='height:50px;max-height:50px;'>
                                        </td>  
                                        <td>{{$value->status_one}}</td>  
                                        <td><a href="{{route('approve_one',$value->id)}}">Approved</a></td>  
                                        <td>
                                            <img class='img-fluid' download src="{{asset('storage/image/'.$value->proof_two)}}" style='height:50px;max-height:50px'>
                                        </td>  
                                        <td>{{$value->status_two}}</td>  
                                        <td><a href="{{route('approve_two',$value->id)}}">Approved</a></td>  
                                        <td><a class='btn btn-danger btn-sm' href="{{route('delete.proof',$value->id)}}">Remove</a></td>      
                                    </tr>
                                </tbody>
                            @empty
                                
                            @endforelse
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
@endsection
