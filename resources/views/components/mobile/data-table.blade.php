@props(['headers' => [], 'emptyMessage' => 'No data available.'])

<div class="mobile-data-table">
    @if($slot->isEmpty())
        <div class="mobile-bezel-outer text-center py-5">
            <div class="mobile-bezel-inner">
                <i class="ri-inbox-2-line" style="font-size: 2.5rem; color: var(--admin-text-dim);"></i>
                <p class="text-muted mt-3 mb-0">{{ $emptyMessage }}</p>
            </div>
        </div>
    @else
        {{ $slot }}
    @endif
</div>

<style>
    /* Add extra padding at the bottom of data tables on mobile to prevent the last item from being hidden behind a bottom navigation bar or floating action button */
    .mobile-data-table {
        padding-bottom: 80px;
    }
</style>
