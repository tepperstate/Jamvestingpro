<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile | Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css" integrity="sha512-xA6Hp6oezhjd6LiLZynuukm80f8BoZ3OpcEYaqKoCV3HKQDrYjDE1Gu8ocxgxoXmwmSzM4iqPvCsOkQNiu41GA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('asset/bootstrap.js')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('asset/icheck.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('asset/adminlite.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('asset/scrobal.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('asset/datepicker.css')}}">
</head>
<style>
	.my_card{
    /* background: rgb(0,0,4) !important;
    background: linear-gradient(90deg, darkgreen 0%, black 48%, darkblue 96%)!important; */
	background-image: linear-gradient(320.54deg, #00069F 0%, #120010 72.37%), linear-gradient(58.72deg, #69D200 0%, #970091 100%), linear-gradient(121.28deg, #8CFF18 0%, #6C0075 100%), linear-gradient(121.28deg, #8000FF 0%, #000000 100%), linear-gradient(180deg, #00FF19 0%, #24FF00 0.01%, #2400FF 100%), linear-gradient(52.23deg, #0500FF 0%, #FF0000 100%), linear-gradient(121.28deg, #32003A 0%, #FF4040 100%), radial-gradient(50% 72.12% at 50% 50%, #EB00FF 0%, #110055 100%);
    background-blend-mode: screen, color-dodge, color-burn, screen, overlay, difference, color-dodge, normal;

	/* background: rgb(1,0,9);
    background: linear-gradient(90deg, rgba(1,0,9,1) 0%, rgba(1,12,54,1) 46%, rgba(4,22,1,1) 86%) ! important; */
	/* background: rgb(1,0,9); */
    /* background: linear-gradient(90deg, rgba(1,0,9,1) 0%, rgba(103,99,2,1) 46%, rgba(15,83,2,1) 86%); */
    color:white !important;
    border-radius: 10px !important;
    /* box-shadow: 0 3px 10px rgb(0 0 0 / 0.2) !important; */
    box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
    border:1px solid rgb(83, 82, 82);
    }
	@media (max-width:700px){
		.padding{
			margin-left: 9px;
			margin-right: 9px;
		}
	}
</style>
<body>
   <div class="container">
      <br>
      <br>
      <br>
      <br>
      <div class='col-lg-6 mt-4 pt-4 card my_card mx-auto'>
         <br>
         <br>
         <div class='card-body'>
            <form method='post'  action="{{route('reset.post.post')}}">
               @csrf
               <p style='margin-bottom:25px; text-align:center'>change your password</p>
               @error('email')
                     <p style='color:red'>{{$message}}</p>
                @enderror
               @if(session('success'))
                  <p style='color:green;font-size:14px;'>{{session('success')}}</p>
               @endif
               @if(session('info'))
                  <p style='color:green;font-size:14px;'>{{session('info')}}</p>
               @endif
               <label for="">password</label>
               <input class='form-control' name='password' type="password" placeholder="enter new password">

               <input type="hidden" name='token' value='{{$token}}'>
               <input type="hidden" name='email' value='{{$email}}'>
               @error('password')
                  <small style='color:red'>{{$message}}</small>
                  <br>
               @enderror
                  <br>
               <button class='btn btn-primary'>Submit</button>
            </form>
         </div>
         <br>
         <br>
      </div>

   </div>
    @if(session('status'))
      <script>
         toastr.success("{{session('status')}}","success")
      </script>
    @endif
</body>
</html>
