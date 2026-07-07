<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" />
    <title>Customize Your Trading Environment</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Core Icons & Libraries -->
    <link rel="stylesheet" href="{{ asset('new/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    @livewireStyles

    <style>
        body {
            font-family: "Inter", "Outfit", sans-serif !important;
            margin: 0;
            padding: 0;
            background: #000000;
        }
        
        .glass-card {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    
    @livewire('onboarding-wizard')

    @livewireScripts
</body>
</html>
