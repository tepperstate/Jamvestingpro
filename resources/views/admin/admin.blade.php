@extends('layouts.admin.app')
@section('title', 'Staff Management')

@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void(0)">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>New Administrator</h4>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12 mb-3'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <div class="logo-bg-premium mb-3" style="width: 220px; height: 44px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: transparent; border-radius: 12px; padding: 2px 6px;">
                           <x-ui.logo variant="light" size="lg" />
                        </div>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Administrator Registration</h4>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('admin.add')}}" id="submit" class='row' >
                           @csrf
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Admin Name</label>
                              <input type='text' class='form-control' name='name' id="name" required>
                           </div>
                           <div class='col-lg-4 mt-1'>
                              <label class='font-text'>Admin Email</label>
                              <input type='text' class='form-control' name='email' id="email" required>
                           </div>
                           <div class='col-lg-4 mt-1'>
                           <label class='font-text'>Password</label>
                           <input type='text' class='form-control' name='password' id="password" required>
                           </div>
                           <button id="button" class='background button-btn' style='color:#fff'>SAVE</button>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 border-color mb-4">
               <div class="card-body">
                  <p>Admins</p>
                  <hr>
                  <div class="table-responsive">
                     <table id="table" class="table table-bordered table-striped"  width="100%" cellspacing="0">
                        <thead>
                           <tr style='font-size:15px'>
                              <th>SN</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody style='font-size:14px'>
                        @forelse($admin as $key => $admins)
                              <tr>
                                 <td>{{++$key}}</td>
                                 <td>{{$admins->name}}</td>
                                 <td>{{$admins->email}}</td>
                                 @if($admins->status ==2)
                                    <td class="text-info font-weight-bold">Super Admin</td>
                                 @elseif($admins->status == 1)
                                    <td class="text-success font-weight-bold">Active</td>
                                 @elseif($admins->status == 3)
                                    <td class="text-secondary">Reserved</td>
                                 @elseif($admins->status ==0)
                                     <td class="text-danger font-weight-bold">Suspended</td>
                                 @endif
                                 @if($admins->status == 2 )
                                   <td>not allowed</td>
                                 @elseif($admins->status == 3 )
                                   <td>not allowed</td>
                                 @else
                                   <td>
                                      <a class='btn btn-sm btn-primary' status="{{$admins->status}}" did='{{$admins->id}}' href='javascript:void(0)'>Update</a>
                                      <a class='btn btn-sm btn-danger' href='{{route("delete_amdin",$admins->id)}}'>delete</a>
                                   </td>
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
   </div>
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
   <script>
      $(document).ready(function() {
         $('#table').DataTable()

         $(document).on('click','.btn-primary',function(){
         let attr = $(this).attr('did');
         let status =  $(this).attr('status')

         if(confirm('Are sure you want to proceed')){
            
            $.post("{{route('admin.admin.update')}}",{id:attr,status:status},function(data){
               
               toastr.success("admin status updated","success")
               setTimeout(function(){
                  location.reload()
               },3000)
            })
         } 
      })

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      });
   </script>
@endsection
