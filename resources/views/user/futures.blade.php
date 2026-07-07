@extends('layouts.user.app')
@section('content')
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
