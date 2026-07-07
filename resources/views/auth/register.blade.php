@extends('layouts.user.app')
@section('content')
<div class="vh-100 d-flex justify-content-center">
  <div class="form-access my-auto">
    <form id="btn" method='post' action="{{route('register.post')}}">
      @csrf
      <span>Create Account</span>
        @if(session('status'))
          <p class='alert alert-danger'>{{session('status')}}</p>
        @endif
      <div class="form-group">
        <input type="text"  name='first_name' class="form-control" placeholder="first Name" value='{{old("first_name")}}'>
        @error('first_name')
          <small class='text-danger'>{{$message}}</small>
        @enderror
      </div>
      <div class="form-group">
        <input type="email" name='email' class="form-control" placeholder="Email Address" value='{{old("email")}}'>
        @error('email')
          <small class='text-danger'>{{$message}}</small>
        @enderror
      </div>
      <div class="form-group">
        <input type="password" name='password' class="form-control" placeholder="Password" value=''>
        @error('password')
          <small class='text-danger'>{{$message}}</small>
        @enderror
      </div>
      <div class="form-group">
        <input type="password" name='password_confirmation' class="form-control" placeholder="Confirm Password">
        @error('password_confirmation')
          <small class='text-danger'>{{$message}}</small>
        @enderror
      </div>
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="form-checkbox" checked>
        <label class="custom-control-label" for="form-checkbox">I agree to the <a href="signup-dark_1638453.html">Terms &
            Conditions</a></label>
      </div>
      <button type="submit" class="btn btn-primary">Create Account</button>
    </form>
    <h2>Already have an account? <a href="/">Sign in here</a></h2>
  </div>
</div>

<script>
    let btn  = document.getElementById('btn')    

    btn.addEventListener('submit',(event)=>{
      let check = document.getElementById('form-checkbox')

      if(check.checked == false){
         event.preventDefault();
         return
      }
    })
</script>
@endsection
