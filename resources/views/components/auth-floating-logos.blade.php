<style>
    /* === Floating Logos Background Component === */
    .floating-bg {
        position: fixed;
        inset: 0;
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
        background: #060b18; /* Ensure consistent base background */
    }
    
    /* Re-adding orbs/grid if they aren't in the parent */
    .floating-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 20%, rgba(255, 51, 51, 0.06) 0%, transparent 50%),
                    radial-gradient(ellipse at 50% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
        opacity: 0.8;
    }
    .floating-bg-grid {
        position: absolute;
        inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), 
                          linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    .float-logo {
        position: absolute;
        opacity: 0.12;
        filter: blur(0.4px);
        transition: opacity 0.5s ease;
        will-change: transform;
    }
    .float-logo img {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }
    
    @keyframes float-type-1 {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(120px, -180px) rotate(30deg); }
    }
    @keyframes float-type-2 {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-140px, -100px) rotate(-20deg); }
    }
    @keyframes float-type-3 {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(100px, 140px) rotate(15deg); }
    }
    @keyframes float-type-4 {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-80px, 160px) rotate(-25deg); }
    }

    /* Distribution Matrix (24 logos) */
    .l-1 { top: 10%; left: 5%; animation: float-type-1 18s infinite alternate ease-in-out; }
    .l-2 { top: 15%; right: 5%; animation: float-type-2 22s infinite alternate ease-in-out; }
    .l-3 { bottom: 10%; left: 5%; animation: float-type-3 20s infinite alternate ease-in-out; }
    .l-4 { bottom: 15%; right: 5%; animation: float-type-4 24s infinite alternate ease-in-out; }
    
    .l-5 { top: 40%; left: 8%; animation: float-type-2 19s infinite alternate ease-in-out; }
    .l-6 { top: 45%; right: 8%; animation: float-type-1 21s infinite alternate ease-in-out; }
    .l-7 { top: 5%; left: 30%; animation: float-type-3 23s infinite alternate ease-in-out; }
    .l-8 { bottom: 5%; right: 30%; animation: float-type-4 25s infinite alternate ease-in-out; }
    
    .l-9 { top: 25%; left: 25%; animation: float-type-1 17s infinite alternate ease-in-out; animation-delay: -2s; }
    .l-10 { top: 30%; right: 25%; animation: float-type-2 20s infinite alternate ease-in-out; animation-delay: -3s; }
    .l-11 { bottom: 25%; left: 40%; animation: float-type-3 22s infinite alternate ease-in-out; animation-delay: -1s; }
    .l-12 { bottom: 30%; right: 40%; animation: float-type-4 19s infinite alternate ease-in-out; animation-delay: -4s; }
    
    .l-13 { top: 60%; left: 15%; animation: float-type-2 21s infinite alternate ease-in-out; }
    .l-14 { top: 65%; right: 15%; animation: float-type-3 23s infinite alternate ease-in-out; }
    .l-15 { top: 12%; left: 55%; animation: float-type-1 26s infinite alternate ease-in-out; }
    .l-16 { bottom: 12%; right: 55%; animation: float-type-4 20s infinite alternate ease-in-out; }
    
    .l-17 { top: 75%; left: 35%; animation: float-type-3 22s infinite alternate ease-in-out; animation-delay: -5s; }
    .l-18 { top: 80%; right: 35%; animation: float-type-2 18s infinite alternate ease-in-out; animation-delay: -2s; }
    .l-19 { top: 20%; left: 75%; animation: float-type-1 24s infinite alternate ease-in-out; }
    .l-20 { bottom: 20%; right: 75%; animation: float-type-4 21s infinite alternate ease-in-out; }
    
    .l-21 { top: 35%; left: 48%; animation: float-type-2 20s infinite alternate ease-in-out; }
    .l-22 { bottom: 35%; right: 48%; animation: float-type-3 25s infinite alternate ease-in-out; }
    .l-23 { top: 55%; left: 65%; animation: float-type-1 22s infinite alternate ease-in-out; }
    .l-24 { bottom: 55%; right: 65%; animation: float-type-4 23s infinite alternate ease-in-out; }

    @media (max-width: 768px) {
        .float-logo img { width: 30px; height: 30px; }
        .l-9, .l-10, .l-11, .l-12, .l-21, .l-22, .l-23, .l-24 { display: none; }
    }
</style>

<div class="floating-bg">
    <div class="floating-bg-grid"></div>
    @php
        $symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA', 'META', 'NFLX', 'NVDA', 'BRK.B', 'DIS', 'PYPL', 'ADBE', 'CRM', 'AMD', 'INTC', 'BABA', 'TSM', 'JNJ', 'V', 'MA', 'WMT', 'KO', 'PEP', 'COST'];
    @endphp
    @foreach($symbols as $index => $symbol)
        <div class="float-logo l-{{ $index + 1 }}">
            <img src="{{ \App\Services\AssetLogoService::getLogoUrl($symbol, 'stock') }}" alt="">
        </div>
    @endforeach
</div>
