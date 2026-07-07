@push('modals')
<!-- Wallet Add/Edit -->
<div class="modal fade" id="wallets" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold" id="edit_name">Asset Interface Account</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{route('add_wallets')}}" class="modal-async-form">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}"><input type="hidden" name="did" id="did">
                    <div class="form-group mb-3"><label class="text-muted small uppercase mb-1">System Name</label><input type="text" name="name" class="form-control glass-panel border-0 text-white" required id="namess"></div>
                    <div class="form-group mb-4"><label class="text-muted small uppercase mb-1">Network Path (Address)</label><input type="text" name="address" class="form-control glass-panel border-0 text-white" required id="address"></div>
                    <div class="custom-control custom-switch mb-4"><input type="checkbox" class="custom-control-input" id="ws" name="status" checked><label class="custom-control-label text-white-50 small" for="ws">Active Broadcast Status</label></div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold" id="btn__">
                        <span class="btn-text">Synchronize Interface</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Demo Balance Modal -->
<div class="modal fade" id="edit_demo_balance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white font-weight-bold">Update Simulator Capital</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" action="{{ route('admin.update_demo_balance') }}" class="modal-async-form">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group mb-4">
                        <label class="text-muted small uppercase mb-1">Exact Demo Balance (USD)</label>
                        <input type="number" step="0.01" name="amount" class="form-control glass-panel border-0 text-white font-weight-bold px-3 py-3" value="{{ $c->demo ?? 0 }}" required>
                    </div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Apply Capital Update</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Global UI Custom Message -->
<div class="modal fade" id="customs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold">Interface Frosted-Glass Overlay</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{route('custom_message')}}" class="modal-async-form">
                    @csrf<input type="hidden" name="user" value="{{$user->id}}">
                    <div class="form-group mb-3"><label class="text-white small">Primary Header Vector</label><input type="text" name="custom_header" class="form-control glass-panel border-0 text-white" value="{{$user->custom_header}}" required></div>
                    <div class="form-group mb-4"><label class="text-white small">Matrix Body Content</label><textarea name="custom_message" class="form-control glass-panel border-0 text-white" id="summernote2">{{ $user->custom_message }}</textarea></div>
                    <button type="submit" class="btn btn-premium btn-block py-3">
                        <span class="btn-text">Update Interface Matrix</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- KYC Matrix Access -->
<div class="modal fade" id="questions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold">Master KYC Matrix Data</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-4 text-white">
                    @forelse($onboarding_responses->groupBy('question.section') as $section => $responses)
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary font-weight-bold text-uppercase">
                            @if($section == 1) EXPERIENCE ANALYTICS
                            @elseif($section == 2) FINANCIAL PROFILE
                            @elseif($section == 3) REGULATORY DECLARATIONS I
                            @elseif($section == 4) REGULATORY DECLARATIONS II
                            @else SECTION {{ $section }}
                            @endif
                        </h6>
                        <hr class="border-white-05">
                        @foreach($responses as $resp)
                        <div class="mb-3">
                            <div class="x-small text-muted text-uppercase">{{ $resp->question->question_text }}</div>
                            <div class="small font-weight-bold">{{ $resp->answer ?? 'N/A' }}</div>
                        </div>
                        @endforeach
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info glass-panel border-0 text-white">
                            No dynamic responses found for this user.
                        </div>
                        <h6 class="text-primary font-weight-bold">LEGACY DATA (READ-ONLY)</h6>
                        <hr class="border-white-05">
                        <div class="row">
                            <div class="col-md-6 mb-3 small opacity-75">EDU: {{$question->education ?? 'N/A'}}</div>
                            <div class="col-md-6 mb-3 small opacity-75">STUDY: {{$question->field_of_study ?? 'N/A'}}</div>
                            <div class="col-md-6 mb-3 small opacity-75">EXP: {{$question->trades_count ?? 'N/A'}}</div>
                            <div class="col-md-6 mb-3 small opacity-75">SOURCE: {{$question->find_us ?? 'N/A'}}</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terminate Account -->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border border-danger shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold">Targeted Deletion</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0 text-center">
                <i data-lucide="alert-triangle" class="text-danger mb-4 mx-auto" style="width: 64px; height: 64px;"></i>
                <h4 class="text-white mb-3">Terminate Account #{{$user->id}}?</h4>
                <p class="text-muted small mb-4">You are about to purge this user from the main ledger. This action is irreversible across all clusters.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn glass-panel text-white flex-grow-1" data-dismiss="modal">Abstain</button>
                    <a href="{{route('admin.user.delete',$user->id)}}" class="btn btn-danger flex-grow-1 d-flex align-items-center justify-content-center">Purge Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Manipulation (Deposit) -->
<div class="modal fade" id="deposit_history" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border border-success shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold">Inbound Ledger Forge</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{route('admin.user.generate_depopsit')}}" class="modal-async-form">
                    @csrf<input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group mb-3"><label class="text-muted small uppercase mb-1">Asset Logic</label><input type="text" name="pay_currency" class="form-control glass-panel border-0 text-white" placeholder="BTC/USDT/USD" required></div>
                    <div class="row g-2 mb-3"><div class="col-6"><label class="text-muted x-small uppercase mb-1">Start Date</label><input type="date" name="start_date" class="form-control glass-panel border-0 text-white" required></div><div class="col-6"><label class="text-muted x-small uppercase mb-1">End Date</label><input type="date" name="end_date" class="form-control glass-panel border-0 text-white" required></div></div>
                    <div class="row g-2 mb-4"><div class="col-6"><label class="text-muted x-small uppercase mb-1">Min Credit</label><input type="number" name="min" class="form-control glass-panel border-0 text-white" value="1000"></div><div class="col-6"><label class="text-muted x-small uppercase mb-1">Max Credit</label><input type="number" name="max" class="form-control glass-panel border-0 text-white" value="5000"></div></div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject Ledger Credits</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Manipulation (Withdrawal) -->
<div class="modal fade" id="deposit_withdrawal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border border-danger shadow-2xl border-0">
            <div class="modal-header border-0 pb-0"><h5 class="modal-title text-white font-weight-bold">Outbound Ledger Forge</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{route('admin.user.generate_with')}}" class="modal-async-form">
                    @csrf<input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group mb-3"><label class="text-muted small uppercase mb-1">Asset Logic</label><input type="text" name="type" class="form-control glass-panel border-0 text-white" placeholder="BTC/USDT/USD" required></div>
                    <div class="row g-2 mb-3"><div class="col-6"><label class="text-muted x-small uppercase mb-1">Start Date</label><input type="date" name="start_date" class="form-control glass-panel border-0 text-white" required></div><div class="col-6"><label class="text-muted x-small uppercase mb-1">End Date</label><input type="date" name="end_date" class="form-control glass-panel border-0 text-white" required></div></div>
                    <div class="row g-2 mb-4"><div class="col-6"><label class="text-muted x-small uppercase mb-1">Min Debit</label><input type="number" name="min" class="form-control glass-panel border-0 text-white" value="100"></div><div class="col-6"><label class="text-muted x-small uppercase mb-1">Max Debit</label><input type="number" name="max" class="form-control glass-panel border-0 text-white" value="1000"></div></div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject Ledger Debits</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Inject History Account Modal -->
<div class="modal fade" id="generate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white font-weight-bold">Inject History Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{ route('admin.user.generate_trade') }}" class="modal-async-form">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">No. of Trades</label>
                            <input type="number" name="no" class="form-control glass-panel border-0 text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Asset</label>
                            <select name="symbols" class="form-control glass-panel border-0 text-white" required>
                                @foreach(\App\Models\Asset::all() as $asset)
                                <option value="{{$asset->symbols}}" class="text-dark">{{$asset->symbols}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Exchange Type</label>
                            <select name="exchangetype" class="form-control glass-panel border-0 text-white" required>
                                <option value="crypto" class="text-dark">Crypto</option>
                                <option value="stock" class="text-dark">Stock</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Type</label>
                            <select name="type" class="form-control glass-panel border-0 text-white" required>
                                <option value="buy" class="text-dark">Buy</option>
                                <option value="sell" class="text-dark">Sell</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Outcome</label>
                        <select name="outcome" class="form-control glass-panel border-0 text-white" required>
                            <option value="win" class="text-dark">Win</option>
                            <option value="loss" class="text-dark">Loss</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Min Amount</label>
                            <input type="number" name="min" class="form-control glass-panel border-0 text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Max Amount</label>
                            <input type="number" name="max" class="form-control glass-panel border-0 text-white" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject Trade History</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Inject AI Account Modal -->
<div class="modal fade" id="bot" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-modal satin-border shadow-2xl border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white font-weight-bold">Inject AI Account (Bot)</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{ route('admin.generate.bot') }}" class="modal-async-form">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group mb-3">
                        <label class="text-muted small uppercase mb-1">Bot</label>
                        <select name="bot" class="form-control glass-panel border-0 text-white" required>
                            @foreach(\App\Models\Bot::all() as $b)
                            <option value="{{$b->id}}" class="text-dark">{{$b->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Start Date</label>
                            <input type="date" name="start_date" class="form-control glass-panel border-0 text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">End Date</label>
                            <input type="date" name="end_date" class="form-control glass-panel border-0 text-white" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Min Amount</label>
                            <input type="number" name="min" class="form-control glass-panel border-0 text-white" required>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small uppercase mb-1">Max Amount</label>
                            <input type="number" name="max" class="form-control glass-panel border-0 text-white" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-premium btn-block py-3 font-weight-bold">
                        <span class="btn-text">Inject AI Account</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function showView(button, sourceId) {
        // Reset all views first
        $('.content-view').hide();
        // Show the selected view
        $(sourceId).show().fadeIn();
        
        // Ensure Lucide icons are recreated if present inside
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    $(document).ready(function() {
        // Rich Text Initiation
        if ($.fn.summernote) {
            $('#summernote, #summernote2').summernote({
                height: 250, theme: 'lite', placeholder: 'Begin secure broadcast established...',
                toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['codeview']]]
            });
        }

        // Reset wallets modal on hide
        $('#wallets').on('hidden.bs.modal', function() {
            const $form = $(this).find('form');
            $form[0].reset();
            $('#did').val('');
            $('#edit_name').text('Asset Interface Account');
            $('#btn__').find('.btn-text').text('Synchronize Interface');
        });
    });

    function editAddress(id, name, address) {
        $('#did').val(id); $('#namess').val(name); $('#address').val(address);
        $('#edit_name').text('Update Interface System');
        $('#btn__').find('.btn-text').text('Start Lifecycle Update');
        $('#wallets').modal('show');
    }
</script>
@endpush
