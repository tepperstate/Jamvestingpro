@props(['title', 'value', 'period' => 'Last 7D'])

<div class="ma-card">
    <div class="flex justify-between items-center" style="margin-bottom: 16px;">
        <div style="font-weight: 600;">{{ $title }}</div>
        <div class="text-sm" style="background: var(--bg-color); padding: 4px 8px; border-radius: 8px;">{{ $period }}</div>
    </div>
    
    <div style="height: 60px; width: 100%; display: flex; align-items: flex-end; gap: 4px; margin-bottom: 16px; border-bottom: 1px solid var(--card-border);">
        <!-- Placeholder for a mini bar chart / sparkline -->
        @for($i = 0; $i < 15; $i++)
            <div style="flex: 1; background: var(--accent-color); opacity: {{ 0.3 + (rand(1, 7) / 10) }}; height: {{ rand(20, 100) }}%; border-top-left-radius: 2px; border-top-right-radius: 2px;"></div>
        @endfor
    </div>
    
    <div class="text-lg">{{ $value }}</div>
</div>
