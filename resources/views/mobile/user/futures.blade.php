@extends('layouts.user.app')
@section('content')
<style>
/* Glassmorphism Mobile Trading Design - Jamvesting Pro */
:root {
    --gold-primary: #990000;
    --gold-glow: rgba(153, 0, 0, 0.4);
    --glass-bg: rgba(20, 22, 28, 0.75);
    --glass-border: rgba(153, 0, 0, 0.15);
}
body, .content-wrapper, .wrapper {
    background: #0d0e12 !important;
    background-image: radial-gradient(circle at 50% 0%, #1a1c24 0%, #0d0e12 70%) !important;
    color: #e0e6ed !important;
    font-family: 'Inter', -apple-system, sans-serif !important;
}
.box, .card, .nav-tabs-custom, .tab-content {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    margin-bottom: 20px !important;
    overflow: hidden;
}
.box-header {
    border-bottom: 1px solid var(--glass-border) !important;
    background: transparent !important;
}
.btn-success, .btn-primary, .btn-info {
    background: linear-gradient(135deg, #f5d76e 0%, #990000 100%) !important;
    border: none !important;
    color: #0d0e12 !important;
    font-weight: 800 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 15px var(--gold-glow) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    transition: all 0.3s ease !important;
}
.btn-success:active, .btn-primary:active {
    transform: translateY(2px) !important;
    box-shadow: 0 2px 8px var(--gold-glow) !important;
}
.text-success, .text-info, span[style*="springgreen"], span[style*="color:green"] {
    color: var(--gold-primary) !important;
    text-shadow: 0 0 10px var(--gold-glow) !important;
}
input.form-control, select.form-control, .input-group-text {
    background: rgba(0, 0, 0, 0.4) !important;
    border: 1px solid var(--glass-border) !important;
    color: #fff !important;
    border-radius: 12px !important;
    padding: 12px !important;
}
input.form-control:focus {
    border-color: var(--gold-primary) !important;
    box-shadow: 0 0 8px var(--gold-glow) !important;
}
.table {
    color: #e0e6ed !important;
}
.table th {
    border-bottom: 2px solid var(--glass-border) !important;
    color: var(--gold-primary) !important;
    text-transform: uppercase;
    font-size: 12px;
}
.table td {
    border-top: 1px solid rgba(255,255,255,0.05) !important;
}
.nav-tabs .nav-link.active {
    background: transparent !important;
    color: var(--gold-primary) !important;
    border-bottom: 3px solid var(--gold-primary) !important;
}
.nav-tabs .nav-link {
    color: #8892a0 !important;
    border: none !important;
}
/* Responsive Mobile Adjustments */
@media (max-width: 768px) {
    .content, .container-full {
        padding: 10px !important;
    }
    .box {
        border-radius: 16px !important;
        padding: 15px !important;
    }
    h3, h4 {
        font-size: 1.2rem !important;
    }
    .row {
        margin-left: -5px;
        margin-right: -5px;
    }
    .col-12 {
        padding-left: 5px;
        padding-right: 5px;
    }
}
</style>
<div class="container-fluid mt-4">
    <h2>Futures Trading</h2>
    <div class="row">
        <div class="col-md-8">
            <div id="tradingview_chart" style="height: 500px; background: #131722; border-radius: 8px;"></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ url('futures/open') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Amount (USD)</label>
                            <input type="number" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group mb-4">
                            <label>Leverage (x)</label>
                            <input type="number" name="leverage" class="form-control" value="10" required>
                        </div>
                        <button type="submit" name="direction" value="long" class="btn btn-success w-100 mb-2">Long</button>
                        <button type="submit" name="direction" value="short" class="btn btn-danger w-100">Short</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/drip-accrual.js') }}"></script>
@endsection

