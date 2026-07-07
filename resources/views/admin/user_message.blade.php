@extends('layouts.admin.app')
@section('content')
@if(session()->has('message'))
      <script>
         toastr.success("{{ session()->get('message') }}","success")
      </script>
   @endif
   @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
  <!-- Begin Page Content -->
  <div class="container-fluid">
  <a onclick="history.back()" href="javascript:void">back</a>

      <!-- Page Heading -->
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-white">{{$user->first_name}}</h1>
      <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
         class="fas fa-download fa-sm text-white-50"></i> user</a>
   </div>
      <!-- Content Row -->
      <div class="row mb-4">
         <div class='col-lg-12'>
            <p class='text-center'>All {{$user->first_name}} Message</p>
            <br>
         </div>
      </div>
      <div class="row mb-4">
         <div class='col-lg-12'>
            <div class="glass-card border-0 mb-4 p-4" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
               <div class="card-header border-bottom border-dark py-3 mb-4">
                  <h5 class="m-0 font-weight-bold outfit" style='color:white;'>Conversation with {{$user->first_name}}</h5>
                  <div class="text-secondary small">Subject: {{ $email->subject }}</div>
               </div>
               <div class="card-body">
                  
                  <div class="thread-container mb-5">
                      @foreach($thread as $msg)
                          @if($msg->sent_to == 'sent')
                              <!-- Message FROM USER -->
                              <div class="message-bubble user-msg mb-4 p-4 rounded-4" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); width: 85%;">
                                  <div class="d-flex align-items-center gap-3 mb-3">
                                      <div class="btn-icon-glass bg-primary-glass text-primary" style="width: 40px; height: 40px; font-size: 1.2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.1);">
                                          <i class="fas fa-user"></i>
                                      </div>
                                      <div>
                                          <div class="text-white fw-bold outfit">{{ $user->first_name }} {{ $user->last_name }}</div>
                                          <div class="text-secondary x-small">{{ $msg->created_at->format('M d - H:i UTC') }}</div>
                                      </div>
                                  </div>
                                  <div class="message-content text-white-50 lh-lg" style="font-size: 1.05rem;">
                                      {!! nl2br(e($msg->message)) !!}
                                  </div>
                              </div>
                          @else
                              <!-- Message FROM ADMIN -->
                              <div class="message-bubble admin-msg mb-4 p-4 rounded-4 ms-auto" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1); width: 85%;">
                                  <div class="d-flex align-items-center gap-3 mb-3 justify-content-end">
                                      <div class="text-end">
                                          <div class="text-white fw-bold outfit">Admin</div>
                                          <div class="text-secondary x-small">{{ $msg->created_at->format('M d - H:i UTC') }}</div>
                                      </div>
                                      <div class="btn-icon-glass bg-secondary-glass text-secondary" style="width: 40px; height: 40px; font-size: 1.2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1);">
                                          <i class="fas fa-shield-alt text-white"></i>
                                      </div>
                                  </div>
                                  <div class="message-content text-white lh-lg text-end" style="font-size: 1.05rem;">
                                      {!! nl2br(e($msg->message)) !!}
                                  </div>
                              </div>
                          @endif
                      @endforeach
                  </div>

                  <div class="reply-box mt-5 pt-4 border-top border-dark">
                      <form action="{{ route('send_message') }}" method="POST">
                          @csrf
                          <input type="hidden" name="user_id" value="{{ $user->id }}">
                          <input type="hidden" name="subject" value="{{ str_starts_with($email->subject, 'Re:') ? $email->subject : 'Re: ' . $email->subject }}">
                          <input type="hidden" name="thread_id" value="{{ $email->thread_id ?? $email->id }}">
                          <input type="hidden" name="reply_to_id" value="{{ $email->id }}">
                          
                          <div class="form-group mb-3">
                              <label class="small text-secondary mb-2 uppercase fw-bold">Admin Reply</label>
                              <textarea name="message" class="form-control" rows="4" placeholder="Type your response here..." required style="background: rgba(255,255,255,0.05); color:white; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;"></textarea>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center mt-4">
                              <button type="submit" class="btn btn-primary px-5 py-2 outfit font-weight-bold ms-auto">
                                  <i class="fas fa-paper-plane me-2"></i> Send Reply
                              </button>
                          </div>
                      </form>
                  </div>

               </div>
            </div>
         </div>
      </div>
      
   </div>
    <!-- Modal -->

@endsection
