<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, viewport-fit=cover">
    <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Registration Questionnaire | {{site()->name}}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <script src="{{asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --bg-deep: #000000;
            --accent-primary: #990000;
            --accent-hover: #660000;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --glass-bg: rgba(0, 0, 0, 0.65);
            --glass-border: rgba(255, 255, 255, 0.08);
            --primary-gradient: linear-gradient(135deg, #990000, #660000);
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: clamp(20px, 5vh, 60px) 20px;
        }

        /* Atmospheric Elements */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            z-index: -1;
            opacity: 0.5;
            animation: float 25s infinite ease-in-out alternate;
        }
        .bg-orb-1 { width: 500px; height: 500px; background: rgba(220, 38, 38, 0.15); top: -100px; left: -100px; }
        .bg-orb-2 { width: 600px; height: 600px; background: rgba(59, 130, 246, 0.12); bottom: -200px; right: -100px; animation-delay: -5s; }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            mask-image: radial-gradient(circle at center, black 30%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at center, black 30%, transparent 85%);
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(80px, 40px) rotate(15deg); }
        }

        .questionnaire-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 32px;
            width: 100%;
            max-width: 720px;
            padding: clamp(24px, 5vw, 48px);
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .satin-border {
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            background-clip: padding-box !important;
        }
        .satin-border::after {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.02) 40%, rgba(255,255,255,0.02) 60%, rgba(255,255,255,0.12));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            -webkit-mask-composite: destination-out;
            pointer-events: none;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        .premium-logo-badge {
            display: inline-flex;
            height: 56px;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .premium-logo-badge:hover { transform: translateY(-3px) scale(1.02); }

        /* Dropdown Visibility Hardening */
        select option {
            background-color: #000000 !important; /* Slate 900 */
            color: #ffffff !important;
            padding: 15px !important;
        }
        
        /* Fix for Safari/Chrome on some OS */
        .form-select option:checked,
        .form-select option:hover {
            background-color: var(--accent-primary) !important;
            color: #fff !important;
        }

        /* Steps System */
        .step-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            position: relative;
            padding: 0 10px;
        }
        .progress-line {
            position: absolute;
            top: 16px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: rgba(255,255,255,0.05);
            z-index: 1;
        }
        .progress-line-active {
            position: absolute;
            top: 16px;
            left: 20px;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), #f87171);
            z-index: 2;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 12px rgba(153, 0, 0, 0.3);
        }
        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1e293b;
            border: 2px solid rgba(255,255,255,0.1);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            position: relative;
            z-index: 3;
            transition: 0.4s;
            font-size: 0.85rem;
        }
        .step-dot.active {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
            box-shadow: 0 0 20px rgba(153, 0, 0, 0.4);
            transform: scale(1.15);
        }
        .step-dot.completed {
            background: #15803d;
            border-color: #15803d;
            color: white;
        }
        .step-label {
            position: absolute;
            top: 42px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.2);
            transition: 0.3s;
        }
        .step-dot.active .step-label { color: var(--accent-primary); opacity: 1; }

        /* Form Logic */
        .slide {
            display: none;
            animation: slideIn 0.5s forwards ease-out;
        }
        .slide.active { display: block; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        h2 { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.8rem; margin-bottom: 8px; color: #fff; }
        .slide-desc { color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 35px; }

        .form-label { font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            border-radius: 12px;
            color: #fff !important;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: 0.3s;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.06) !important;
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 4px rgba(153, 0, 0, 0.15) !important;
        }

        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            border-radius: 14px;
            padding: 14px 32px;
            color: #fff;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            box-shadow: 0 8px 25px rgba(153, 0, 0, 0.3);
        }
        .btn-premium:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(153, 0, 0, 0.4);
            filter: brightness(1.1);
        }
        .btn-premium:active { transform: translateY(0); }

        .btn-glass {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 14px 32px;
            color: var(--text-secondary);
            font-weight: 700;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-glass:hover { background: rgba(255,255,255,0.06); color: #fff; border-color: rgba(255,255,255,0.2); }

        /* Loader */
        .preloader {
            position: fixed; inset: 0; background: var(--bg-deep); z-index: 2000;
            display: flex; align-items: center; justify-content: center; transition: 0.5s;
        }
        .loader-ring {
            width: 60px; height: 60px; border: 4px solid rgba(153,0,0,0.1);
            border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 576px) {
            .questionnaire-card { border-radius: 0; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; }
            .step-label { display: none; }
            .premium-logo-badge {
                width: 210px;
                height: 42px;
                padding: 2px 6px;
            }
        }
    </style>
</head>
<body>
    @include('marketing.partials.ambient')

    <div class="preloader" id="app-preloader">
        <div class="loader-ring"></div>
    </div>

    <div class="questionnaire-card satin-border" data-aos="zoom-in">
        <div class="logo-container">
            <a href="{{url('/')}}">
                <div class="premium-logo-badge">
                    <x-ui.logo variant="light" size="lg" />
                </div>
            </a>
        </div>

        <div class="step-progress">
            <div class="progress-line"></div>
            <div class="progress-line-active" id="progress-line" style="width: 0%;"></div>
            
            <div class="step-dot active" data-step="1">1<div class="step-label">Experience</div></div>
            <div class="step-dot" data-step="2">2<div class="step-label">Financials</div></div>
            <div class="step-dot" data-step="3">3<div class="step-label">Compliance</div></div>
            <div class="step-dot" data-step="4">4<div class="step-label">Finalize</div></div>
        </div>

        <form id="questionnaire-form">
            <div id="slides-wrapper">
                @for($s = 1; $s <= 4; $s++)
                <div class="slide {{ $s == 1 ? 'active' : '' }}" id="slide-{{ $s }}">
                    <h2 class="outfit">
                        @if($s == 1) Investment Background
                        @elseif($s == 2) Economic Profile
                        @elseif($s == 3) Regulatory Disclosure I
                        @elseif($s == 4) Final Attestation
                        @endif
                    </h2>
                    <p class="slide-desc">
                        @if($s == 1) Calibrating your portfolio parameters based on professional expertise.
                        @elseif($s == 2) Essential verification for AML/KYC institutional compliance.
                        @elseif($s == 3) Mandatory declarations required for leveraged trading access.
                        @elseif($s == 4) Signature verification of all provided investment declarations.
                        @endif
                    </p>
                    
                    <div class="slide-fields">
                        @foreach($questions->where('section', $s) as $q)
                        <div class="mb-4">
                            <label class="form-label">{{ $q->title }}</label>
                            @if($q->input_type == 'select')
                                <select class="form-select req-{{ $s }}" name="{{ $q->question_key }}" required>
                                    <option value="">Select Tier/Option</option>
                                    @foreach($q->options ?? [] as $opt)
                                        <option value="{{ $opt->value }}">{{ $opt->label }}</option>
                                    @endforeach
                                </select>
                            @elseif($q->input_type == 'number')
                                <input type="number" class="form-control req-{{ $s }}" name="{{ $q->question_key }}" placeholder="USD Amount" required>
                            @else
                                <input type="text" class="form-control req-{{ $s }}" name="{{ $q->question_key }}" placeholder="Provide declaration..." required>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top border-glass-light">
                        @if($s == 1)
                            <a href="{{ route('question.skip') }}" class="btn-glass">Skip</a>
                            <button type="button" class="btn-premium" onclick="nextStep(1)">Proceed <i class="ri-arrow-right-line ms-1"></i></button>
                        @elseif($s == 4)
                            <button type="button" class="btn-glass" onclick="prevStep(4)">Back</button>
                            <button type="button" class="btn-premium px-5" id="submitBtn" onclick="submitForm()">Active Account <i class="ri-check-double-line ms-1"></i></button>
                        @else
                            <button type="button" class="btn-glass" onclick="prevStep({{ $s }})">Back</button>
                            <button type="button" class="btn-premium" onclick="nextStep({{ $s }})">Next Phase <i class="ri-arrow-right-line ms-1"></i></button>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
        </form>
    </div>

    <script>
        $(window).on('load', () => $('#app-preloader').fadeOut(600));

        function validateStep(step) {
            let isValid = true;
            $(`.req-${step}`).each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    $(this).addClass('border-danger');
                } else {
                    $(this).removeClass('border-danger');
                }
            });
            return isValid;
        }

        $('input, select').on('change', function() { $(this).removeClass('border-danger'); });

        function updateProgress(step) {
            $('.step-dot').removeClass('active completed');
            for(let i=1; i<step; i++) $(`.step-dot[data-step="${i}"]`).addClass('completed');
            $(`.step-dot[data-step="${step}"]`).addClass('active');

            const widths = {1: '0%', 2: '33%', 3: '66%', 4: '100%'};
            $('#progress-line').css('width', widths[step]);
        }

        function nextStep(curr) {
            if(!validateStep(curr)) {
                toastr.error('Verification required for all fields.');
                return;
            }
            $(`#slide-${curr}`).fadeOut(300, () => {
                $(`#slide-${curr+1}`).fadeIn(300).addClass('active');
                updateProgress(curr+1);
            });
        }

        function prevStep(curr) {
            $(`#slide-${curr}`).fadeOut(300, () => {
                $(`#slide-${curr-1}`).fadeIn(300).addClass('active');
                updateProgress(curr-1);
            });
        }

        async function submitForm() {
            if(!validateStep(4)) return toastr.error('Attestation incomplete.');
            
            let btn = $('#submitBtn');
            btn.html('<i class="ri-loader-4-line spin d-inline-block"></i> Securing Data...').prop('disabled', true);

            let formData = {};
            $('#questionnaire-form').serializeArray().forEach(item => formData[item.name] = item.value);

            try {
                const res = await fetch('{{route("store")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(formData)
                });

                const data = await res.json();
                if(data.status) {
                    toastr.success('Institutional Access Granted.');
                    setTimeout(() => location.href = "/", 2000);
                }
            } catch (e) {
                toastr.error('Synchronization failed.');
                btn.html('Active Account <i class="ri-check-double-line ms-1"></i>').prop('disabled', false);
            }
        }
    </script>
</body>
</html>
