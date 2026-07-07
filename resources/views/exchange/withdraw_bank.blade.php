@extends('layouts.user.app')
@section("content")

<div class="container-full  mt-4">
  <div class="content-header" style="padding:15px 13px 0px">
    <h3>Withdrawal History</h3>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/user"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#">Bank Transfer Withdrawal History</a></li>
    </ol> 
  </div> 
  <section class="content" style="padding:15px 13px 0px">
    <div class="row">
      <div class="col-lg-12">
        <div class="card my_card">
          <div class="card-header border-0">
            <h3 class="card-title" style="color: white;">Bank Transfer Withdrawal History</h3>
          </div>
          <div class="card-body">
            <div class="card-body table-responsive p-0">
                
              <table id="example" class="table table-head-fixed text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date and Time</th>
                  <th>Amount</th>
                  <th>Bank Name</th>
                  <th>Bank Address</th>
                  <th>Account Name</th>
                  <th>Account Address</th>
                  <th>Iban</th>
                  <th>Swift/BIC</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody style="color: white;font-size:13px">
                  @forelse ($data as $key =>  $val )
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$val->created_at}}</td>
                        <td>${{number_format($val->amount)}}</td>
                        <td>{{$val->bank_name}}</td>
                        <td>{{$val->bank_address}}</td>
                        <td>{{$val->account_name}}</td>
                        <td>{{$val->account_address}}</td>
                        <td>{{$val->iban}}</td>
                        <td>{{$val->swift}}</td>
                        @if($val->status ==='pending')
                            <td class='text-danger'>{{$val->status}}</td>
                        @else
                        <td class='text-success'>{{$val->status}}</td>

                        @endif
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
  </section>
</div>
@if(session('status'))
   <script>
      toastr.success("{{session('status')}}",'successful')
   </script>
  @endif

  <script>
     $(document).ready(function(){
        $('#example').DataTable();
     })
  </script>
@endsection
