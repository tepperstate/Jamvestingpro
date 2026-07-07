@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Bank Wire Transfer Request</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.bank-wire.store') }}" method="POST">
                        @csrf
                        
                        <h5 class="mb-3 border-bottom pb-2">Transfer Details</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="100" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                                </div>
                                <small class="text-muted">Minimum $100.00</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select name="currency" id="currency" class="form-select" required>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2">Bank Details</h5>
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                <input type="text" name="account_holder_name" id="account_holder_name" class="form-control" value="{{ old('account_holder_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="routing_number" class="form-label">Routing Number (US Only)</label>
                                <input type="text" name="routing_number" id="routing_number" class="form-control" value="{{ old('routing_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="swift_bic" class="form-label">SWIFT / BIC Code</label>
                                <input type="text" name="swift_bic" id="swift_bic" class="form-control" value="{{ old('swift_bic') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="iban" class="form-label">IBAN (International)</label>
                                <input type="text" name="iban" id="iban" class="form-control" value="{{ old('iban') }}">
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2">Bank Address</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="bank_country" class="form-label">Country Code (e.g. US, GB)</label>
                                <input type="text" name="bank_country" id="bank_country" class="form-control" value="{{ old('bank_country', 'US') }}" maxlength="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_city" class="form-label">City</label>
                                <input type="text" name="bank_city" id="bank_city" class="form-control" value="{{ old('bank_city') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_state" class="form-label">State / Province</label>
                                <input type="text" name="bank_state" id="bank_state" class="form-control" value="{{ old('bank_state') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_zip" class="form-label">Postal / Zip Code</label>
                                <input type="text" name="bank_zip" id="bank_zip" class="form-control" value="{{ old('bank_zip') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="bank_address" class="form-label">Full Address</label>
                                <textarea name="bank_address" id="bank_address" rows="2" class="form-control">{{ old('bank_address') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="user_notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea name="user_notes" id="user_notes" rows="2" class="form-control">{{ old('user_notes') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Transfer Request</button>
                            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
