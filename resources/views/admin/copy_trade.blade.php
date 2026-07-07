@extends('layouts.admin.app')
@section('content')
  <!-- Begin Page Content -->
  <div class="container-fluid">
     <a onclick="history.back()" href="javascript:void">back</a>
      <!-- Page Heading -->
      <div class="text-center justify-content-center mb-4">
         <h4 class="font-weight-bold m-0" style='color:var(--text-primary) !important'>Add CopyTradeers</h4>
         <p>These are asset that are avalible for trade by the client.</p>
      </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12 mb-4'>
            <div class="glass-card border-0 mb-4 shadow p-2">
               <div class="card-body">
                  <div class='row'>
                     <div class='col-lg-3'>
                        <h4 class='font-weight-bold' style='color:var(--text-primary) !important'>Add Copy Traders</h4>
                        <p>These are Copy  that are avalible for trade by the client</p>
                     </div>
                     <div class='col-lg-9'>
                        <form method='post' action="{{route('add.store_trader')}}" id="submit" class='row' enctype="multipart/form-data">
                           @csrf
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Trader Image</label>
                              <input type='file' class='form-control' name='image'  required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Trade Name</label>
                              <input type='text' class='form-control' name='name' id="min" required>
                              @error('name')
                                 <small>{{$message}}</small>
                              @enderror
                           </div>
                           <div class="col-lg-6 mb-3">
                              <label class='font-text'>Buffer Percentage (%)</label>
                              <input type="number" step="0.01" name="buffer_percent" class='form-control text-white bg-black-soft border-glass' value="20" required>
                           </div>
                           <div class="col-lg-6 mb-3">
                              <label class='font-text'>Per Withdrawal (%)</label>
                              <input type="number" step="0.01" name="per_withdrawal_percent" class='form-control text-white bg-black-soft border-glass' value="5" required>
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Country</label>
                              <input type='text' class='form-control' name='country' id="min" required  placeholder="Nigeria">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Roi</label>
                              <input type='text' class='form-control'  name='percentage' id="min" required  placeholder="please dont enter text only number or decimals">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Amount</label>
                              <input type='number' class='form-control' name='amount' id="min" required  placeholder="200">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Win or Loss</label>
                              <input type='text' class='form-control' name='win' id="min" required  placeholder="yes or no">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Total Copier</label>
                              <input type='number' class='form-control' name='total_copier' id="min" required  placeholder="50">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Min Loss</label>
                              <input type='number' class='form-control' name='min_loss' id="min" required  placeholder="">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Max Loss</label>
                              <input type='number' class='form-control' name='max_loss' id="min" required  placeholder="">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Min Profit</label>
                              <input type='number' class='form-control' name='min_win' id="min" required  placeholder="">
                           </div>
                           <div class='col-lg-6 mt-1'>
                              <label class='font-text'>Max Profit</label>
                              <input type='number' class='form-control' name='max_win' id="min" required  placeholder="">
                           </div>
                        
                           <!-- <div class='col-lg-6 mt-1'>
                                 <label class='font-text'>Action</label>
                                 <select class='form-control' name='action' id="actions" required>
                                    <option value="">Select Action</option>
                                    <option value="win">Is Win</option>
                                    <option value="loss">Is Loss</option>
                                 </select>
                           </div> -->
                           <script>
                              document.getElementById('submit').addEventListener('submit', function(e) {
                                 var winField = document.querySelector('#actions');
                                 if (winField.value.toLowerCase() !== 'win' && winField.value.toLowerCase() !== 'loss') {
                                    e.preventDefault();
                                    alert('Win or Loss');
                                    winField.focus();
                                 }
                              });
                           </script>
                        
                          
                           <div class='col-lg-12'>

                             <input type="submit" style="margin-left:0" class='background button-btn' style='color:#fff'>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
       >
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
