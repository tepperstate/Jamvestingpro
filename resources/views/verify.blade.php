<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	 <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Account Verification | {{site()->name}}</title>
  
	<!-- Bootstrap 4.0-->
	<link rel="stylesheet" href="{{asset('new/vendor_components/bootstrap/dist/css/bootstrap.min.css')}}">
	
	<!-- theme style -->
	<link rel="stylesheet" href="{{asset('css/style.css')}}">
	
	<!-- Admin skins -->
	<link rel="stylesheet" href="{{asset('css/skin_color.css')}}">	

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<style>
		.brand-logo { 
			text-align: center; 
			margin-bottom: 30px; 
			display: flex;
			justify-content: center;
			width: 100%;
		}
		        .logo-bg-premium {
            background-color: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: auto;
            min-height: 80px;
            margin-bottom: 20px;
        }
        .logo-bg-premium img {
            width: 100%;
            max-width: 320px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }

        
	</style>

</head>
<body style="background: #060b18; color: #e2e8f0; font-family: 'Inter', sans-serif;">
    @include('marketing.partials.ambient')
	
<div class="auth-2-outer row align-items-center h-p100 m-0" style="position: relative; z-index: 2;">
	<div class="auth-2">
		<!-- /.login-logo -->
		<div class="auth-body" style="padding:40px 30px">
		<div class="brand-logo">
			<a href="{{url('/')}}" class="logo-bg-premium">
                <img src="{{asset('assets/img/favicon.svg')}}" alt="{{site()->name}}">
            </a>
		</div>
		<p class="auth-msg text-black-50 text-center">Account verification successful</p>
		<br>
		<br>
		@if(session('error'))
			<p class='alert alert-danger' style="background:red">{{session('error')}}</p>
		@endif
		<p class="alert alert-success" style="background:green">Your Account has beeen verified you can login <a href="{{route('login')}}">Here</a></p>
		</div>
	</div>

</div>

	
@if(session('error'))
   <script>
      toastr.error("{{session('error')}}",'error')
   </script>
@endif

	<!-- jQuery 3 -->
	<script src="{{asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js')}}"></script>
	
	<!-- fullscreen -->
	<script src="{{asset('new/vendor_components/screenfull/screenfull.js')}}"></script>
	
	<!-- popper -->
	<script src="{{asset('vendor_components/popper/dist/popper.min.js')}}"></script>
	
	<!-- Bootstrap 4.0-->
	<script src="{{asset('assets/vendor_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

@if(session('status'))
   <script>
      toastr.success("{{session('status')}}",'successful')
   </script>
@endif


@if(session('error'))
   <script>
      toastr.success("{{session('error')}}",'error')
   </script>
@endif

</body>
</html>
