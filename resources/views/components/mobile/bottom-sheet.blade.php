@props(['id' => 'sheet', 'title' => ''])

<!-- Full-Screen Action Panel -->
<div class="mobile-bottom-sheet" id="{{ $id }}">
    <div class="mobile-bottom-sheet-header">
        @if($title)
            <div class="mobile-bottom-sheet-title">{{ $title }}</div>
        @else
            <div></div>
        @endif
        <button type="button" class="mobile-bottom-sheet-close" onclick="closeBottomSheet('{{ $id }}')">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div class="mobile-bottom-sheet-content">
        {{ $slot }}
    </div>
</div>

<script>
    if (typeof window.closeBottomSheet === 'undefined') {
        window.closeBottomSheet = function(id) {
            document.getElementById(id).classList.remove('active');
            // Re-enable body scroll
            document.body.style.overflow = '';
        }
        
        window.openBottomSheet = function(id) {
            document.getElementById(id).classList.add('active');
            // Disable body scroll so background page stays fixed
            document.body.style.overflow = 'hidden';
        }
    }
</script>
