@props(['trades' => [], 'isAdmin' => false])

<div class="table-responsive trade-history-wrapper">
    <table class="table table-dark table-hover border-0 mb-0" style="font-size:0.85rem;">
        <thead>
            <tr class="text-secondary" style="border-bottom: 1px solid rgba(255,255,255,0.05); font-weight: 600; text-transform: uppercase;">
                <th class="border-0 px-3 py-3">Date / Time</th>
                <th class="border-0 px-3 py-3">Asset</th>
                <th class="border-0 px-3 py-3 text-center">Direction</th>
                <th class="border-0 px-3 py-3 text-right">Strike / End</th>
                <th class="border-0 px-3 py-3 text-right">Investment</th>
                <th class="border-0 px-3 py-3 text-right">Payout</th>
                <th class="border-0 px-3 py-3 text-center">Result</th>
                @if($isAdmin)
                <th class="border-0 px-3 py-3 text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody class="animated-trade-list">
            @forelse($trades as $t)
            <tr class="trade-row align-middle" style="border-bottom: 1px solid rgba(255,255,255,0.02); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" data-status="{{ strtolower($t->status ?? '') }}">
                <td class="border-0 px-3 py-3">
                    <div class="text-white font-weight-bold">{{ $t->created_at ? $t->created_at->format('M d, Y') : 'N/A' }}</div>
                    <div class="text-secondary" style="font-size: 0.7rem;">{{ $t->created_at ? $t->created_at->format('H:i:s A') : '' }}</div>
                </td>
                <td class="border-0 px-3 py-3">
                    <div class="d-flex align-items-center gap-2">
                        @php $rawSymbol = $t->symbol ?? 'Unknown'; @endphp
                        <x-asset-logo :symbol="$rawSymbol" size="xs" />
                        <span class="font-weight-bold">{{ $rawSymbol }}</span>
                    </div>
                </td>
                <td class="border-0 px-3 py-3 text-center">
                    @if(strtolower($t->type ?? '') == 'call' || strtolower($t->type ?? '') == 'buy')
                        <div class="text-success d-flex flex-column align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span style="font-size:0.65rem; font-weight:800; margin-top: 2px;">CALL</span>
                        </div>
                    @else
                        <div class="text-danger d-flex flex-column align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
                            <span style="font-size:0.65rem; font-weight:800; margin-top: 2px;">PUT</span>
                        </div>
                    @endif
                </td>
                <td class="border-0 px-3 py-3 text-right">
                    <div class="text-white font-weight-bold">${{ number_format($t->bought_price ?? $t->open_price ?? 0, 2) }}</div>
                    <div class="text-secondary" style="font-size:0.75rem;">${{ number_format($t->sold_price ?? $t->end_price ?? 0, 2) }}</div>
                </td>
                <td class="border-0 px-3 py-3 text-right font-weight-bold" style="font-size: 1rem;">
                    ${{ number_format($t->amount ?? 0, 2) }}
                </td>
                <td class="border-0 px-3 py-3 text-right font-weight-bold {{ (strtolower($t->status ?? '') == 'win' || ($t->payout ?? 0) > ($t->amount ?? 0)) ? 'text-success' : 'text-secondary' }}" style="font-size: 1rem;">
                    ${{ number_format($t->payout ?? 0, 2) }}
                </td>
                <td class="border-0 px-3 py-3 text-center">
                    @php
                        $st = strtolower($t->status ?? 'pending');
                        $badgeClass = 'badge-secondary text-white';
                        if(in_array($st, ['win', 'won', 'success'])) $badgeClass = 'bg-success text-white';
                        elseif(in_array($st, ['loss', 'lost', 'failed'])) $badgeClass = 'bg-danger text-white';
                        elseif(in_array($st, ['running', 'working', 'pending'])) $badgeClass = 'bg-warning text-dark';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius:8px; font-size:0.7rem; font-weight:800; letter-spacing:0.5px; border:none; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        {{ strtoupper($st) }}
                    </span>
                </td>
                @if($isAdmin)
                <td class="border-0 px-3 py-3 text-center">
                    <button class="btn btn-sm glass-panel border-0 text-white" style="border-radius: 8px; padding: 6px 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                    </button>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center py-5 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-inbox mb-3 opacity-50"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                    <h6 class="font-weight-bold">No Trade History</h6>
                    <p class="small mb-0">No records found for this view.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@once
@push('css')
<style>
    .trade-row:hover {
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        cursor: default;
        z-index: 10;
        position: relative;
    }
    .bg-success { background-color: var(--accent-success, #ff3333) !important; }
    .bg-danger { background-color: var(--accent-danger, #ef4444) !important; }
    .bg-warning { background-color: var(--accent-warning, #f59e0b) !important; }
    
    .trade-history-wrapper {
        border-radius: 16px;
        overflow: hidden;
    }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof lucide !== 'undefined') lucide.createIcons();
        
        // Initial list entrance animation
        anime({
            targets: '.animated-trade-list .trade-row',
            translateY: [30, 0],
            opacity: [0, 1],
            delay: anime.stagger(80, {start: 100}),
            easing: 'easeOutQuint',
            duration: 800
        });

        // Add subtle flash effect indicating realtime settlement
        const rows = document.querySelectorAll('.animated-trade-list .trade-row');
        rows.forEach((row, index) => {
            if (index > 2) return; // Only flash the most recent entries
            
            const status = row.getAttribute('data-status');
            let color = 'transparent';
            
            if(status === 'win' || status === 'won') {
                color = 'rgba(255, 51, 51, 0.15)';
            } else if(status === 'loss' || status === 'lost') {
                color = 'rgba(239, 68, 68, 0.15)';
            }
            
            if (color !== 'transparent') {
                setTimeout(() => {
                    anime({
                        targets: row,
                        backgroundColor: [color, 'transparent'],
                        duration: 1500,
                        easing: 'easeOutExpo'
                    });
                }, index * 200 + 1000);
            }
        });
    });
</script>
@endpush
@endonce
