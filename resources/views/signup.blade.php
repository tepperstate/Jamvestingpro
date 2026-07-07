<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Create Account | {{site()->name}}</title>
    <meta name="description" content="Create a free account on {{site()->name}} to access global markets, real-time analytics, and professional trading tools.">
    <meta name="keywords" content="sign up, create account, trading platform, crypto, mutual funds, {{site()->name}}">
    <meta property="og:title" content="Create Account | {{site()->name}}">
    <meta property="og:description" content="Create a free account on {{site()->name}} to access global markets, real-time analytics, and professional trading tools.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @notifyCss
    {!! ToastMagic::styles() !!}
    {!! laravelGeoGenius()->initIntlPhoneInput() !!}
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #050505;
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
        }

        .grid-overlay {
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px; pointer-events: none;
        }
        .auth-wrapper { position: relative; z-index: 2; display: flex; min-height: 100vh; }
        .auth-form-side { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-visual-side {
            flex: 0 0 40%; display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .auth-visual-side::before { content: ''; position: absolute; inset: 0; background: url("{{asset('asset/login_v2.png')}}") center/cover no-repeat; opacity: 0.6; filter: none; }
        .auth-visual-side::after { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(5,5,5,0.85), rgba(5,5,5,0.45)); }
        .visual-content { position: relative; z-index: 2; text-align: center; padding: 60px; }
        .visual-content h2 { font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg, #990000, #ffcccc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 16px; }
        .visual-content p { color: rgba(255,255,255,0.85); font-size: 1rem; line-height: 1.7; max-width: 350px; margin: 0 auto; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }

        .auth-card-shell {
            width: 100%; max-width: 600px;
            background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 36px;
            padding: 10px; box-shadow: 0 40px 80px rgba(0,0,0,0.4);
            animation: cardSlideUp 0.8s cubic-bezier(0.32,0.72,0,1);
        }
        .auth-card {
            width: 100%; background: #050505;
            backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08); border-radius: calc(36px - 10px);
            padding: 40px 36px; box-shadow: inset 0 1px 1px rgba(255,255,255,0.15);
        }
        @keyframes cardSlideUp { from { opacity: 0; transform: translateY(60px); filter: blur(10px); } to { opacity: 1; transform: translateY(0); filter: blur(0); } }
        .brand-logo { 
            text-align: center; 
            margin-bottom: 28px; 
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .logo-bg-premium {
            display: inline-flex;
            transition: transform 0.3s ease;
            height: 60px;
            align-items: center;
        }

        .logo-bg-premium:hover { transform: scale(1.02); }

        @media (max-width: 768px) {
            .logo-bg-premium {
                display: inline-flex;
                transition: transform 0.3s ease;
                height: 60px;
                align-items: center;
            }
            .auth-card-shell { padding: 6px; border-radius: 24px; }
            .auth-card { padding: 28px 20px; border-radius: calc(24px - 6px); }
        }
        .auth-card h1 { font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 6px; color: #f1f5f9; }
        .auth-card .subtitle { text-align: center; color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 28px; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control {
            width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px; padding: 12px 14px; color: #ffffff !important; font-size: 0.9rem;
            font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.4s cubic-bezier(0.32,0.72,0,1); outline: none;
        }
        .form-control:focus { background: rgba(255,255,255,0.06); border-color: rgba(153, 0, 0, 0.5); box-shadow: 0 0 0 4px rgba(153, 0, 0, 0.1); }
        .form-control::placeholder { color: rgba(255,255,255,0.25); }
        .form-control:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px rgba(0, 0, 0,0.9) inset !important; -webkit-text-fill-color: #f1f5f9 !important; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='rgba(255,255,255,0.4)' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; }
        select.form-control option { background: #000000; color: #e2e8f0; }

        .btn-auth-primary {
            width: 100%; padding: 8px 8px 8px 24px; background: #ffffff;
            color: #050505; border: none; border-radius: 9999px; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: all 0.7s cubic-bezier(0.32,0.72,0,1); text-transform: uppercase; letter-spacing: 1px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .btn-auth-primary:active { transform: scale(0.98); }
        .btn-auth-primary .icon-wrapper { width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; transition: transform 0.7s cubic-bezier(0.32,0.72,0,1); }
        .btn-auth-primary:hover .icon-wrapper { transform: translate(2px, -1px) scale(1.05); }

        .auth-divider { display: flex; align-items: center; margin: 20px 0; gap: 16px; }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08); }
        .auth-divider span { font-size: 0.8rem; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 1px; }

        .btn-google {
            width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; color: #e2e8f0; font-size: 0.9rem; font-weight: 500; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 12px; transition: all 0.3s; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-google:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2); transform: scale(0.98); }
        .btn-google svg { width: 20px; height: 20px; }

        .auth-footer { text-align: center; margin-top: 24px; font-size: 0.9rem; color: rgba(255,255,255,0.5); }
        .auth-footer a { color: #990000; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { color: #ffcccc; }

        .preloader-wrapper { position: fixed; inset: 0; z-index: 9999; background: #060b18; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 24px; transition: opacity 0.5s ease; }
        .preloader-wrapper .logo-bg-premium {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoPulse 2s ease-in-out infinite;
        }

        .preloader-spinner { width: 40px; height: 40px; border: 3px solid rgba(255,255,255,0.1); border-top-color: #990000; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes logoPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 768px) {
            .auth-visual-side { display: none !important; }
            .auth-form-side { padding: 12px; align-items: flex-start; width: 100%; }
            .auth-card-shell { margin-top: 10px; }
            .auth-card { 
                padding: 24px 16px; 
                border-radius: calc(20px - 6px); 
                width: 100%;
                max-width: 100%;
            }
            .auth-card h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    @include('marketing.partials.ambient')
    <div class="preloader-wrapper" id="preloader">
        <div class="logo-bg-premium">
            <x-ui.logo variant="light" size="lg" />
        </div>
        <div class="preloader-spinner"></div>
    </div>

    <div class="auth-wrapper" id="auth-wrapper" style="display:none;">
        <div class="auth-form-side">
            <div class="auth-card-shell">
            <div class="auth-card">
                <div class="brand-logo">
                    <a href="{{url('/')}}" class="logo-bg-premium" aria-label="Homepage">
                        <x-ui.logo variant="light" size="lg" />
                    </a>
                </div>
                <h1>Create Your Account</h1>
                <p class="subtitle">Start trading with institutional-grade tools</p>

                <form id="signupForm">
                    <input type="hidden" id="invitation_code" name="invitation_code" value="{{$id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" placeholder="First name" required autocomplete="given-name" aria-label="First Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" placeholder="Last name" required autocomplete="family-name" aria-label="Last Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" placeholder="you@example.com" required autocomplete="email" aria-label="Email Address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone number" required autocomplete="tel" aria-label="Phone Number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" class="form-control" name="country" required aria-label="Country">
                                    <option value="">Select Country</option>
                                    <option value="Afghanistan">Afghanistan</option><option value="Albania">Albania</option><option value="Algeria">Algeria</option><option value="Andorra">Andorra</option><option value="Angola">Angola</option><option value="Argentina">Argentina</option><option value="Armenia">Armenia</option><option value="Australia">Australia</option><option value="Austria">Austria</option><option value="Azerbaijan">Azerbaijan</option><option value="Bahamas">Bahamas</option><option value="Bahrain">Bahrain</option><option value="Bangladesh">Bangladesh</option><option value="Barbados">Barbados</option><option value="Belarus">Belarus</option><option value="Belgium">Belgium</option><option value="Belize">Belize</option><option value="Benin">Benin</option><option value="Bhutan">Bhutan</option><option value="Bolivia, Plurinational State of">Bolivia</option><option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option><option value="Botswana">Botswana</option><option value="Brazil">Brazil</option><option value="Brunei Darussalam">Brunei</option><option value="Bulgaria">Bulgaria</option><option value="Burkina Faso">Burkina Faso</option><option value="Burundi">Burundi</option><option value="Cambodia">Cambodia</option><option value="Cameroon">Cameroon</option><option value="Canada">Canada</option><option value="Cape Verde">Cape Verde</option><option value="Central African Republic">Central African Republic</option><option value="Chad">Chad</option><option value="Chile">Chile</option><option value="China">China</option><option value="Colombia">Colombia</option><option value="Congo">Congo</option><option value="Costa Rica">Costa Rica</option><option value="Croatia">Croatia</option><option value="Cuba">Cuba</option><option value="Cyprus">Cyprus</option><option value="Czech Republic">Czech Republic</option><option value="Denmark">Denmark</option><option value="Djibouti">Djibouti</option><option value="Dominican Republic">Dominican Republic</option><option value="Ecuador">Ecuador</option><option value="Egypt">Egypt</option><option value="El Salvador">El Salvador</option><option value="Estonia">Estonia</option><option value="Ethiopia">Ethiopia</option><option value="Fiji">Fiji</option><option value="Finland">Finland</option><option value="France">France</option><option value="Gabon">Gabon</option><option value="Gambia">Gambia</option><option value="Georgia">Georgia</option><option value="Germany">Germany</option><option value="Ghana">Ghana</option><option value="Greece">Greece</option><option value="Guatemala">Guatemala</option><option value="Guinea">Guinea</option><option value="Guyana">Guyana</option><option value="Haiti">Haiti</option><option value="Honduras">Honduras</option><option value="Hong Kong">Hong Kong</option><option value="Hungary">Hungary</option><option value="Iceland">Iceland</option><option value="India">India</option><option value="Indonesia">Indonesia</option><option value="Iran, Islamic Republic of">Iran</option><option value="Iraq">Iraq</option><option value="Ireland">Ireland</option><option value="Israel">Israel</option><option value="Italy">Italy</option><option value="Jamaica">Jamaica</option><option value="Japan">Japan</option><option value="Jordan">Jordan</option><option value="Kazakhstan">Kazakhstan</option><option value="Kenya">Kenya</option><option value="Korea, Republic of">South Korea</option><option value="Kuwait">Kuwait</option><option value="Kyrgyzstan">Kyrgyzstan</option><option value="Latvia">Latvia</option><option value="Lebanon">Lebanon</option><option value="Liberia">Liberia</option><option value="Libya">Libya</option><option value="Liechtenstein">Liechtenstein</option><option value="Lithuania">Lithuania</option><option value="Luxembourg">Luxembourg</option><option value="Madagascar">Madagascar</option><option value="Malawi">Malawi</option><option value="Malaysia">Malaysia</option><option value="Maldives">Maldives</option><option value="Mali">Mali</option><option value="Malta">Malta</option><option value="Mexico">Mexico</option><option value="Moldova, Republic of">Moldova</option><option value="Monaco">Monaco</option><option value="Mongolia">Mongolia</option><option value="Montenegro">Montenegro</option><option value="Morocco">Morocco</option><option value="Mozambique">Mozambique</option><option value="Myanmar">Myanmar</option><option value="Namibia">Namibia</option><option value="Nepal">Nepal</option><option value="Netherlands">Netherlands</option><option value="New Zealand">New Zealand</option><option value="Nicaragua">Nicaragua</option><option value="Niger">Niger</option><option value="Nigeria">Nigeria</option><option value="Norway">Norway</option><option value="Oman">Oman</option><option value="Pakistan">Pakistan</option><option value="Panama">Panama</option><option value="Papua New Guinea">Papua New Guinea</option><option value="Paraguay">Paraguay</option><option value="Peru">Peru</option><option value="Philippines">Philippines</option><option value="Poland">Poland</option><option value="Portugal">Portugal</option><option value="Qatar">Qatar</option><option value="Romania">Romania</option><option value="Russian Federation">Russia</option><option value="Rwanda">Rwanda</option><option value="Saudi Arabia">Saudi Arabia</option><option value="Senegal">Senegal</option><option value="Serbia">Serbia</option><option value="Singapore">Singapore</option><option value="Slovakia">Slovakia</option><option value="Slovenia">Slovenia</option><option value="Somalia">Somalia</option><option value="South Africa">South Africa</option><option value="South Sudan">South Sudan</option><option value="Spain">Spain</option><option value="Sri Lanka">Sri Lanka</option><option value="Sudan">Sudan</option><option value="Sweden">Sweden</option><option value="Switzerland">Switzerland</option><option value="Taiwan, Province of China">Taiwan</option><option value="Tanzania, United Republic of">Tanzania</option><option value="Thailand">Thailand</option><option value="Togo">Togo</option><option value="Trinidad and Tobago">Trinidad and Tobago</option><option value="Tunisia">Tunisia</option><option value="Turkey">Turkey</option><option value="Uganda">Uganda</option><option value="Ukraine">Ukraine</option><option value="United Arab Emirates">United Arab Emirates</option><option value="United Kingdom">United Kingdom</option><option value="United States">United States</option><option value="Uruguay">Uruguay</option><option value="Uzbekistan">Uzbekistan</option><option value="Venezuela, Bolivarian Republic of">Venezuela</option><option value="Viet Nam">Vietnam</option><option value="Yemen">Yemen</option><option value="Zambia">Zambia</option><option value="Zimbabwe">Zimbabwe</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select id="currency" class="form-control" name="currency" required aria-label="Currency">
                                    <option selected value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Create password" required autocomplete="new-password" aria-label="Password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" required autocomplete="new-password" aria-label="Confirm Password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth-primary mt-2" aria-label="Create Account">
                        <span>Create Account</span>
                        <div class="icon-wrapper" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </button>
                </form>

                <div class="auth-divider"><span>or</span></div>

                <button class="btn-google" onclick="location.href='{{route('login.google')}}'">
                    <svg viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                    Sign Up with Google
                </button>

                <div class="auth-footer">
                    Already have an account? <a href="{{route('login')}}">Sign In</a>
                </div>
            </div>
            </div>
        </div>

        <div class="auth-visual-side d-none d-lg-flex">
            <div class="visual-content">
                <h2>Join Thousands<br>of Traders.</h2>
                <p>Create your free account and access global markets, real-time analytics, and professional trading tools.</p>
            </div>
        </div>
    </div>

    <script>
        function hidePreloader() {
            const preloader = document.getElementById('preloader');
            const wrapper = document.getElementById('auth-wrapper');
            if (preloader && preloader.style.display !== 'none') {
                preloader.style.opacity = '0';
                setTimeout(() => { 
                    preloader.style.display = 'none'; 
                    if (wrapper) wrapper.style.display = 'flex'; 
                }, 500);
            }
        }

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }

        // Failsafe: Hide preloader after 5 seconds no matter what
        setTimeout(hidePreloader, 5000);

        const safeToastr = {
            success: (msg, title) => { if (typeof toastr !== 'undefined') toastr.success(msg, title); else console.log('Success:', msg); },
            error: (msg, title) => { if (typeof toastr !== 'undefined') toastr.error(msg, title); else console.error('Error:', msg); }
        };

        document.getElementById('signupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
            const preloader = document.getElementById('preloader');
            const wrapper = document.getElementById('auth-wrapper');

            const data = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                country: document.getElementById('country').value,
                currency: document.getElementById('currency').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
                invitation_code: document.getElementById('invitation_code').value || null,
            };

            if (preloader) { preloader.style.display = 'flex'; preloader.style.opacity = '1'; }
            if (wrapper) wrapper.style.display = 'none';

            try {
                const response = await fetch("{{route('s.post')}}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.errors) {
                    for (var key in result.errors) {
                        if (result.errors.hasOwnProperty(key)) {
                            safeToastr.error(result.errors[key][0], 'Error');
                        }
                    }
                    if (preloader) preloader.style.display = 'none'; 
                    if (wrapper) wrapper.style.display = 'flex';
                }

                if (result.status) {
                    safeToastr.success(result.status, 'Success');
                    localStorage.setItem('tutorialSkipped', '');
                    setTimeout(() => { location.href = "{{route('onboarding.wizard')}}" }, 2000);
                }
            } catch (error) {
                console.error(error);
                safeToastr.error("Registration is currently unavailable. Please try again shortly.", 'Error');
                if (preloader) preloader.style.display = 'none'; 
                if (wrapper) wrapper.style.display = 'flex';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('notify::components.notify')
    @notifyJs
    {!! ToastMagic::scripts() !!}
</body>
</html>
