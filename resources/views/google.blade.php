<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Login {{site()->name}}</title>
  
	<!-- Bootstrap 4.0-->
	<link rel="stylesheet" href="{{asset('new/vendor_components/bootstrap/dist/css/bootstrap.min.css')}}">
	
	<!-- theme style -->
	<link rel="stylesheet" href="{{asset('css/style.css')}}">
	
	<!-- Admin skins -->
	<link rel="stylesheet" href="{{asset('css/skin_color.css')}}">	
	<script src="{{asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js')}}"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
<style>
      .my_card{
		background:  rgb(43 48 64 / 1);
		color:white !important;
		border-radius: 10px !important;
		border: 1px solid #aaadb0;
    }
	@media (max-width:700px){
		.padding{
			margin-left: 9px;
			margin-right: 9px;
		}
	}
	#loader-wrapper {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 9999; /* Ensure it overlays all other content */
		background-color: rgba(255, 255, 255, 0.8); /* Optional: to add a white overlay */
	}
	#loader-container {
		position: absolute;
		width: 100%;
		height: 100%;
		object-fit: cover;
		filter: brightness(40%); /* Adjust brightness to reduce color intensity */
	}

	@keyframes bounce {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-20px); }
	}

	.site-name {
		position: absolute;
		bottom: 20px; /* Position at the bottom of the wrapper */
		color: #3498db; /* Optional: match the spinner color */
		font-size: 24px; /* Adjust font size as needed */
		font-weight: bold;
		z-index: 2; /* Ensure it stays in front of the image */
		animation: bounceText 1.5s infinite; /* Add bouncing animation */
		top: 59%; /* Adjust the vertical position to be closer to the spinner */

	}

	@keyframes bounceText {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-30px); }
	}

	.spinner {
		position: relative;
		width: 60px;
		height: 60px;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 50%;
		margin-left: -75px;
		z-index: 999;
	}

	.spinner span {
		position: absolute;
		top: 50%;
		left: var(--left);
		width: 35px;
		height: 7px;
		background: #ffff;
		animation: dominos 1s ease infinite;
		box-shadow: 2px 2px 3px 0px black;
	}

	.spinner span:nth-child(1) {
	--left: 80px;
	animation-delay: 0.125s;
	}

	.spinner span:nth-child(2) {
	--left: 70px;
	animation-delay: 0.3s;
	}

	.spinner span:nth-child(3) {
	left: 60px;
	animation-delay: 0.425s;
	}

	.spinner span:nth-child(4) {
	animation-delay: 0.54s;
	left: 50px;
	}

	.spinner span:nth-child(5) {
	animation-delay: 0.665s;
	left: 40px;
	}

	.spinner span:nth-child(6) {
	animation-delay: 0.79s;
	left: 30px;
	}

	.spinner span:nth-child(7) {
	animation-delay: 0.915s;
	left: 20px;
	}

	.spinner span:nth-child(8) {
	left: 10px;
	}

	@keyframes dominos {
	50% {
		opacity: 0.7;
	}

	75% {
		-webkit-transform: rotate(90deg);
		transform: rotate(90deg);
	}

	80% {
		opacity: 1;
	}
	}
</style>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const loaderWrapper = document.getElementById("loader-wrapper");
		const content = document.getElementById("wrapper");

		// Show the loader container
		loaderWrapper.style.display = "flex";

		// Simulate loading
		window.addEventListener("load", function() {
			// Hide the loader container
			loaderWrapper.style.display = "none";
			
			// Show the content
			content.style.display = "block";
			
			// Allow scrolling after content is loaded
			document.body.style.overflow = "auto";
		});
    })
</script>
<div class="d-flex align-items-center justify-content-center" style='height:100vh'>
   <div id="loader-wrapper">
        <img id="loader-container" src="{{asset('assets/img/app.jpg')}}" alt="Loading...">
        <div class="spinner">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
		<p class="site-name">{{site()->name}}</p>
    </div>
	<div id="wrapper" class="auth-body my_card padding">
		<br>
		<br>
		<div class="brand-logo" style="text-align: center; margin-bottom: 28px; display: flex; justify-content: center; width: 100%;">
			<div class="logo-bg-premium" style="display: flex; width: 100%; justify-content: center; align-items: center; margin-bottom: 20px; background: transparent;">
				<x-ui.logo variant="light" size="lg" />
			</div>
		</div>

		<p class="auth-msg text-light">Enter 2fa code from your Google Authenticator </p>
		<br>
		<p id="display"></p>
		
		<form id="otp"  method="post" class="form-element">
			@csrf
			<div class="form-group has-feedback border pl-2">
				<input type="text" name="code" id="otp_value" class="form-control" placeholder="Enter 2fa" style="color:white" required>
				<span class="ion ion-locked form-control-feedback text-dark"></span>
			</div>
			<div class="col-12 text-center">
				<button type="submit" class="btn btn-md my-20 btn-success">Submit</button>
			</div>
		</form>
        <br>
		 
	</div>
</div>


<script>
  let otp = document.getElementById("otp")
  if (otp) {
	otp.addEventListener('submit', async (e) => {
		e.preventDefault()
	
		let small = document.getElementById("small")
		const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

		const display = document.getElementById('display')

		let sub = document.getElementById("wrapper")
	
		const spinner = document.getElementById('loader-wrapper')

		let otp_value = document.getElementById("otp_value").value

		let data ={
			code:otp_value
		}


		const options = {
			method: 'post',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken,
			},
			body:JSON.stringify(data)
		}
	
		spinner.style.display = 'flex';
		sub.style.display = "none"
	
		try {
			const response = await fetch("{{route('google2fa')}}", options)

			const data = await response.json();

			if (data.errors) {

				let { errors } = data

				for (var key in errors) {
				if (errors.hasOwnProperty(key)) {
					toastr.error(errors[key][0], 'error');
				}
				}
				spinner.style.display = 'none';
				sub.style.display = "block"
			}

			if (data.error) {
				toastr.error(data.error, 'error')
				display.innerText = data.error;
				display.style.color = 'red'
				spinner.style.display = 'none';
				sub.style.display = "block"
				return;
			}

			if (data.status) {
				toastr.success(data.status, 'success')
	
				setTimeout(() => {
				  location.href = "{{ route('dashboard.index') }}"
				}, 1500)
			}
		} catch (error) {
			console.log(error)
			toastr.error("Please try again later", 'error')
			spinner.style.display = 'none';
			sub.style.display = "block"
		}
	})
  }
</script>

</body>
</html>
