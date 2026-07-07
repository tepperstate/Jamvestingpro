// Drip Accrual Numerical Animation Engine
(function() {
    function parseNumber(text) {
        if (!text) return 0;
        let cleaned = text.trim().replace(/[$,\s+%]/g, '');
        // handle negative parenthesis formats like ($10.00)
        if (text.includes('(') && text.includes(')')) {
            cleaned = '-' + cleaned.replace(/[()]/g, '');
        }
        let num = parseFloat(cleaned);
        return isNaN(num) ? 0 : num;
    }

    function getFormat(text) {
        return {
            hasDollar: text.includes('$'),
            hasPercent: text.includes('%'),
            hasPlus: text.startsWith('+') || text.includes(' +'),
            hasMinus: text.startsWith('-') || text.includes(' -') || (text.includes('(') && text.includes('$'))
        };
    }

    function formatNumber(value, format) {
        let formatted = Math.abs(value).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        let result = '';
        if (value < 0) {
            result += '-';
        } else if (format.hasPlus && value > 0) {
            result += '+';
        }
        if (format.hasDollar) result += '$';
        result += formatted;
        if (format.hasPercent) result += '%';
        return result;
    }

    function animateValue(el, start, end, format) {
        if (el.dataset.dripAnimating === "true") return;
        el.dataset.dripAnimating = "true";

        let obj = { val: start };
        let duration = 1200;

        if (typeof anime !== 'undefined') {
            anime({
                targets: obj,
                val: end,
                round: 100, // round to 2 decimals
                easing: 'easeOutQuad',
                duration: duration,
                update: function() {
                    el.innerText = formatNumber(obj.val, format);
                },
                complete: function() {
                    el.dataset.dripAnimating = "false";
                    // Set final exact value to prevent roundoff errors
                    el.innerText = formatNumber(end, format);
                }
            });
        } else {
            // Fallback requestAnimationFrame loop
            let startTime = null;
            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                let progress = Math.min((timestamp - startTime) / duration, 1);
                // Ease out quad formula
                let easeProgress = progress * (2 - progress);
                let currentVal = start + (end - start) * easeProgress;
                el.innerText = formatNumber(currentVal, format);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.dataset.dripAnimating = "false";
                    el.innerText = formatNumber(end, format);
                }
            }
            window.requestAnimationFrame(step);
        }
    }

    const selectors = [
        '.drip-accrual',
        '.bal',
        '.portfolio-value',
        '.trade-amt',
        '.asset-price-val',
        '.available-bal',
        '.in-trade-bal',
        '.net-profit-val'
    ];

    function applyDripAccrual(el) {
        if (el.dataset.dripInitialized === "true") return;
        el.dataset.dripInitialized = "true";

        let text = el.innerText.trim();
        let endVal = parseNumber(text);
        if (endVal === 0 && text !== '0' && text !== '$0.00' && text !== '0.00%') return;

        let format = getFormat(text);
        // Start from 0 (or value * 0.8 if value is large, to make it subtle)
        let startVal = endVal > 1000 ? endVal * 0.8 : 0;
        
        animateValue(el, startVal, endVal, format);

        // Observe future updates to this element
        let observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (el.dataset.dripAnimating === "true") return;
                
                let newText = el.innerText.trim();
                let newTarget = parseNumber(newText);
                
                // If the new text is different and not already matching the formatting animation target
                if (newTarget !== endVal) {
                    let oldVal = endVal;
                    endVal = newTarget;
                    let newFormat = getFormat(newText);
                    
                    // Temporarily disconnect to avoid loops
                    observer.disconnect();
                    animateValue(el, oldVal, newTarget, newFormat);
                    // Reconnect after animation setup
                    setTimeout(() => {
                        observer.observe(el, { characterData: true, childList: true, subtree: true });
                    }, 50);
                }
            });
        });

        observer.observe(el, { characterData: true, childList: true, subtree: true });
    }

    function initAll() {
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                applyDripAccrual(el);
            });
        });
    }

    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Expose utility globally
    window.initDripAccrual = initAll;
    window.applyDripAccrualSingle = applyDripAccrual;
})();
