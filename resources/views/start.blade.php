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
  	<link rel="stylesheet" href="{{asset('css/style.css')}}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Admin skins -->
	<link rel="stylesheet" href="{{asset('css/skin_color.css')}}">	
        
	<!-- Bootstrap 4.0-->
	<link rel="stylesheet" href="{{asset('new/vendor_components/bootstrap/dist/css/bootstrap.css')}}">

</head>
<style>
	.my_card {
		background: rgba(23, 28, 48, 0.8) !important;
		backdrop-filter: blur(20px) !important;
		-webkit-backdrop-filter: blur(20px) !important;
		border: 1px solid rgba(255, 255, 255, 0.1) !important;
		border-radius: 24px !important;
		padding: 40px 30px !important;
		box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
		color: white !important;
		transition: transform 0.3s ease, box-shadow 0.3s ease;
	}
	.my_card:hover {
		transform: translateY(-5px);
		box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.6) !important;
	}
	.kyc-illustration {
		width: 140px;
		height: 140px;
		margin: 0 auto 24px;
		display: block;
		filter: drop-shadow(0 0 15px rgba(153, 0, 0, 0.3));
		animation: float 4s ease-in-out infinite;
	}
	@keyframes float {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-10px); }
	}
	.btn-premium-continue {
		background: linear-gradient(135deg, #990000, #5c0000) !important;
		border: none !important;
		padding: 12px 28px !important;
		border-radius: 12px !important;
		font-weight: 700 !important;
		text-transform: uppercase !important;
		letter-spacing: 0.5px !important;
		box-shadow: 0 10px 15px -3px rgba(153, 0, 0, 0.3) !important;
		transition: all 0.3s ease !important;
	}
	.btn-premium-continue:hover {
		transform: scale(1.05);
		box-shadow: 0 20px 25px -5px rgba(153, 0, 0, 0.4) !important;
	}
	.btn-premium-skip {
		background: rgba(255, 255, 255, 0.05) !important;
		border: 1px solid rgba(255, 255, 255, 0.1) !important;
		padding: 12px 28px !important;
		border-radius: 12px !important;
		font-weight: 600 !important;
		color: rgba(255, 255, 255, 0.7) !important;
		transition: all 0.3s ease !important;
	}
	.btn-premium-skip:hover {
		background: rgba(255, 255, 255, 0.1) !important;
		color: white !important;
	}
	.auth-msg {
		font-size: 1.75rem !important;
		font-weight: 800 !important;
		margin-bottom: 12px !important;
		background: linear-gradient(135deg, #f1f5f9, #94a3b8);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}
	.kyc-instructions {
		color: rgba(255, 255, 255, 0.6);
		font-size: 0.95rem;
		line-height: 1.6;
		margin-bottom: 32px;
		max-width: 400px;
		margin-left: auto;
		margin-right: auto;
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
		background-color: #151719; /* Optional: to add a white overlay */
	}
	#loader-container {
		position: absolute;
		width: 100%;
		height: 100%;
		object-fit: cover;
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

		.loader {
	position: relative;
	width: 54px;
	height: 54px;
	border-radius: 10px;
	}

	.loader div {
	width: 8%;
	height: 24%;
	background: rgb(128, 128, 128);
	position: absolute;
	left: 50%;
	top: 30%;
	opacity: 0;
	border-radius: 50px;
	box-shadow: 0 0 3px rgba(0,0,0,0.2);
	animation: fade458 1s linear infinite;
	}

	@keyframes fade458 {
	from {
		opacity: 1;
	}

	to {
		opacity: 0.25;
	}
	}

	.loader .bar1 {
	transform: rotate(0deg) translate(0, -130%);
	animation-delay: 0s;
	}

	.loader .bar2 {
	transform: rotate(30deg) translate(0, -130%);
	animation-delay: -1.1s;
	}

	.loader .bar3 {
	transform: rotate(60deg) translate(0, -130%);
	animation-delay: -1s;
	}

	.loader .bar4 {
	transform: rotate(90deg) translate(0, -130%);
	animation-delay: -0.9s;
	}

	.loader .bar5 {
	transform: rotate(120deg) translate(0, -130%);
	animation-delay: -0.8s;
	}

	.loader .bar6 {
	transform: rotate(150deg) translate(0, -130%);
	animation-delay: -0.7s;
	}

	.loader .bar7 {
	transform: rotate(180deg) translate(0, -130%);
	animation-delay: -0.6s;
	}

	.loader .bar8 {
	transform: rotate(210deg) translate(0, -130%);
	animation-delay: -0.5s;
	}

	.loader .bar9 {
	transform: rotate(240deg) translate(0, -130%);
	animation-delay: -0.4s;
	}

	.loader .bar10 {
	transform: rotate(270deg) translate(0, -130%);
	animation-delay: -0.3s;
	}

	.loader .bar11 {
	transform: rotate(300deg) translate(0, -130%);
	animation-delay: -0.2s;
	}

	.loader .bar12 {
	transform: rotate(330deg) translate(0, -130%);
	animation-delay: -0.1s;
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
    @keyframes logoPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
	
</style>
<script>
	const safeToastr = {
		success: (msg, title) => { if (typeof toastr !== 'undefined') toastr.success(msg, title); else console.log('Success:', msg); },
		error: (msg, title) => { if (typeof toastr !== 'undefined') toastr.error(msg, title); else console.error('Error:', msg); }
	};

	function hideLoader() {
		const loaderWrapper = document.getElementById("loader-wrapper");
		const content = document.getElementById("wrapper");
		if (loaderWrapper && loaderWrapper.style.display !== "none") {
			loaderWrapper.style.display = "none";
			if (content) {
				content.style.display = "block";
				document.body.style.overflow = "auto";
			}
		}
	}

	document.addEventListener("DOMContentLoaded", function() {
		if (document.readyState === "complete") {
			hideLoader();
		} else {
			window.addEventListener("load", hideLoader);
		}
		// Failsafe: Hide loader after 5 seconds
		setTimeout(hideLoader, 5000);
    });
</script>
<body style="background-color: #1c1f2d !important;">
    @include('marketing.partials.ambient')
    <div class="container" style="position: relative; z-index: 2;">
        <div id="loader-wrapper">
            <img id="loader-container" src="{{asset('assets/img/app2.png')}}" alt="Loading..." onerror="this.style.display='none'; hideLoader();">
            <div class="logo-bg-premium">
                <img src="{{asset('assets/img/favicon.svg')}}" alt="{{site()->name}}">
            </div>
            <div class="loader">
                <div class="bar1"></div>
                <div class="bar2"></div>
                <div class="bar3"></div>
                <div class="bar4"></div>
                <div class="bar5"></div>
                <div class="bar6"></div>
                <div class="bar7"></div>
                <div class="bar8"></div>
                <div class="bar9"></div>
                <div class="bar10"></div>
                <div class="bar11"></div>
                <div class="bar12"></div>
            </div>
            <p class="site-name">{{site()->name}}</p>
        </div>
        <div id="wrapper" class="col-lg-5 mx-auto">
            <div class="d-flex align-items-center justify-content-center" style='height:90vh'>
            <div class="my_card box text-center">
                <div class="box-body">
                    <img src="{{asset('assets/img/kyc_illustration.png')}}" alt="Security" class="kyc-illustration">
                    <h2 class="auth-msg">Secure Your Account</h2>
                    <p class="kyc-instructions">
                        Complete your identity verification to unlock institutional-grade features, higher withdrawal limits, and enhanced security for your digital assets.
                    </p>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 20px;">
                        <a class="btn-premium-skip" href="{{route('dashboard.index')}}">Skip for now</a>
                        <a class="btn-premium-continue" href="{{route('profile')}}">Verify Identity</a>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</body>
<script>
	let form = document.getElementById("form")
    if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault()
    
        let small = document.getElementById("small")
        const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

        const display = document.getElementById('display')

        let sub = document.getElementById("wrapper")
    
        const spinner = document.getElementById('loader-wrapper')

        let email = document.getElementById('email').value;
        let password = document.getElementById('password').value;

        let data = {
            email,
            password,
        };

        const options = {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data)
        }
    
        spinner.style.display = 'flex';
        sub.style.display = "none"
    
        try {
            const response = await fetch("{{route('login.post')}}", options)
            const data = await response.json();
                if (data.errors) {
                    let { errors } = data
                    for (var key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        safeToastr.error(errors[key][0], 'error');
                        if(display) display.innerText = errors[key][0];
                    }
                    }
                    if(spinner) spinner.style.display = 'none';
                    if(sub) sub.style.display = "block"
                }

            if (data.error) {
                safeToastr.error(data.error, 'error')
                if(display) {
                    display.innerText = data.error;
                    display.style.color = 'red'
                }
                if(spinner) spinner.style.display = 'none';
                if(sub) sub.style.display = "block"
                return;
            }else{
                safeToastr.success('Login successful', 'success')
                if(display) {
                    display.innerText = "Login successful";
                    display.style.color = 'white'
                    display.style.backgroundColor = 'green'
                    display.style.padding = '10px'
                }
                
                setTimeout(() => {
                location.href = "/otp"
                }, 3000)
            
            }

        } catch (error) {
            console.log(error)
            safeToastr.error("You can't login at the moment please try again later", 'error')
            spinner.style.display = 'none';
            sub.style.display = "block"
        }
    })
    }

</script>

</html>
