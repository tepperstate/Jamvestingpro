/**
 * Live Market 24h Change — Hybrid WebSocket + REST Polling
 * Tries Binance WebSocket first. If blocked, falls back to REST polling
 * via data-api.binance.vision (confirmed accessible).
 */
(function() {
    window.liveMarketChanges = {};

    var WS_URL = 'wss://stream.binance.com:9443/ws/!ticker@arr';
    var REST_URL = 'https://data-api.binance.vision/api/v3/ticker/24hr';
    var wsConnected = false;
    var restInterval = null;
    var socket = null;

    // ─── DOM update logic (shared by both WS and REST) ───
    function applyChanges(symbolChanges) {
        var els = document.querySelectorAll('[data-live-change-symbol]');
        if (els.length === 0) return;

        els.forEach(function(el) {
            var sym = el.getAttribute('data-live-change-symbol');
            if (typeof symbolChanges[sym] === 'undefined') return;

            var pct = symbolChanges[sym];
            window.liveMarketChanges[sym] = pct;

            var formatted = pct.toFixed(2);
            var sign = pct >= 0 ? '+' : '';
            var display = sign + formatted + '%';

            if (el.hasAttribute('data-is-header')) {
                try {
                    if (typeof currentSymbol !== 'undefined' && currentSymbol === sym) {
                        currentChange = pct;
                        if (typeof currentPrice !== 'undefined') {
                            var abs = (currentPrice * pct / 100).toFixed(2);
                            el.innerText = (pct >= 0 ? '+' : '') + abs + ' ' + display;
                        } else {
                            el.innerText = display;
                        }
                    }
                } catch(e) { /* skip */ }
            } else {
                el.innerText = display;
            }

            if (pct >= 0) {
                el.classList.remove('red', 'text-danger');
                el.classList.add('green', 'text-success');
            } else {
                el.classList.remove('green', 'text-success');
                el.classList.add('red', 'text-danger');
            }
        });
    }

    // ─── WebSocket approach ───
    function tryWebSocket() {
        try {
            socket = new WebSocket(WS_URL);
        } catch(e) {
            startRestPolling();
            return;
        }

        var wsTimeout = setTimeout(function() {
            // If no message received within 10s, WS is probably blocked
            if (!wsConnected) {
                try { socket.close(); } catch(e) {}
                startRestPolling();
            }
        }, 10000);

        socket.onopen = function() {};

        socket.onmessage = function(event) {
            wsConnected = true;
            clearTimeout(wsTimeout);

            var data = JSON.parse(event.data);
            var changes = {};
            data.forEach(function(t) {
                changes[t.s] = parseFloat(t.P);
            });
            applyChanges(changes);
        };

        socket.onerror = function() {
            clearTimeout(wsTimeout);
            if (!wsConnected) {
                startRestPolling();
            }
        };

        socket.onclose = function() {
            if (wsConnected) {
                // Was connected before, try to reconnect WS
                wsConnected = false;
                setTimeout(tryWebSocket, 5000);
            }
            // If never connected, REST polling is already running
        };
    }

    // ─── REST polling fallback ───
    function startRestPolling() {
        if (restInterval) return; // already running
        fetchRestData(); // immediate first fetch
        restInterval = setInterval(fetchRestData, 15000); // every 15 seconds
    }

    function fetchRestData() {
        // Only fetch symbols we actually track on the page
        var els = document.querySelectorAll('[data-live-change-symbol]');
        if (els.length === 0) return;

        var tracked = {};
        els.forEach(function(el) {
            tracked[el.getAttribute('data-live-change-symbol')] = true;
        });
        var symbols = Object.keys(tracked);
        if (symbols.length === 0) return;

        // Fetch only needed symbols in batch (up to ~20 at a time via individual requests is wasteful)
        // Use the full 24hr endpoint but apply client-side filtering
        fetch(REST_URL)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var changes = {};
                data.forEach(function(t) {
                    if (tracked[t.symbol]) {
                        changes[t.symbol] = parseFloat(t.priceChangePercent);
                    }
                });
                applyChanges(changes);
            })
            .catch(function() { /* silent fail, will retry on next interval */ });
    }

    // ─── Start ───
    function init() {
        tryWebSocket();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
