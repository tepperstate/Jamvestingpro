(function() {
    'use strict';

    // SVG Icons Map
    const ICONS = {
        success: `<svg class="noti-icon-draw" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="9 12 11 14 15 10"></polyline></svg>`,
        error: `<svg class="noti-icon-draw" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
        warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
        info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
        loading: `<svg class="noti-icon-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>`,
        'trade-buy': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>`,
        'trade-sell': `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline></svg>`,
        achievement: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`
    };

    let container = null;
    let toastQueue = [];
    const MAX_VISIBLE = 5;

    function initContainer() {
        if (!container) {
            container = document.getElementById('modern-notification-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'modern-notification-container';
                container.setAttribute('aria-live', 'polite');
                container.setAttribute('role', 'status');
                document.body.appendChild(container);
            }
        }
        
        // Hide legacy toastr container if exists
        const legacyContainer = document.getElementById('toast-container');
        if (legacyContainer) {
            legacyContainer.style.display = 'none';
        }
    }

    function removeToast(card, instant = false) {
        if (!card.parentNode) return;
        
        if (instant || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            card.remove();
            processQueue();
        } else {
            card.classList.add('modern-noti-exit');
            card.style.height = card.offsetHeight + 'px';
            card.style.overflow = 'hidden';
            
            // Force reflow
            void card.offsetHeight;
            
            card.style.transition = 'height 0.25s ease-out, margin 0.25s ease-out, padding 0.25s ease-out';
            card.style.height = '0';
            card.style.margin = '0';
            card.style.padding = '0';
            card.style.border = 'none';
            
            setTimeout(() => {
                card.remove();
                processQueue();
            }, 250);
        }
    }

    function processQueue() {
        if (container.children.length < MAX_VISIBLE && toastQueue.length > 0) {
            const next = toastQueue.shift();
            container.appendChild(next);
        }
    }

    function createToast(type, message, title, duration) {
        initContainer();

        const card = document.createElement('div');
        card.className = `modern-noti-card modern-noti-${type}`;
        
        if (type === 'error') {
            card.setAttribute('aria-live', 'assertive');
            card.classList.add('noti-shake');
        }

        const iconHtml = ICONS[type] || ICONS.info;
        
        let html = `
            <div class="noti-icon">${iconHtml}</div>
            <div class="noti-content">
                ${title ? `<div class="noti-title">${title}</div>` : ''}
                <div class="noti-desc">${message}</div>
            </div>
            <button class="noti-close" aria-label="Close notification">&times;</button>
        `;

        if (type !== 'loading') {
            html += `
                <div class="noti-progress">
                    <div class="noti-progress-fill" style="animation-duration: ${duration}ms;"></div>
                </div>
            `;
        }

        card.innerHTML = html;

        const closeBtn = card.querySelector('.noti-close');
        closeBtn.addEventListener('click', () => removeToast(card));

        if (type !== 'loading') {
            let timeoutId;
            const progress = card.querySelector('.noti-progress-fill');
            let startTime = Date.now();
            let remaining = duration;
            let isPaused = false;

            const startTimer = () => {
                if (!isPaused) {
                    timeoutId = setTimeout(() => removeToast(card), remaining);
                    if (progress) progress.style.animationPlayState = 'running';
                }
            };

            const pauseTimer = () => {
                isPaused = true;
                clearTimeout(timeoutId);
                remaining -= (Date.now() - startTime);
                if (progress) progress.style.animationPlayState = 'paused';
            };

            const resumeTimer = () => {
                isPaused = false;
                startTime = Date.now();
                startTimer();
            };

            card.addEventListener('mouseenter', pauseTimer);
            card.addEventListener('mouseleave', resumeTimer);
            
            startTimer();
        }

        if (container.children.length >= MAX_VISIBLE) {
            toastQueue.push(card);
        } else {
            container.appendChild(card);
        }

        return card;
    }

    // Global exposed API
    window.showModernNotification = function(type, message, title, duration) {
        let defaultDuration = 4000;
        if (type === 'achievement') defaultDuration = 7000;
        if (type === 'error') defaultDuration = 6000;
        
        createToast(type, message, title, duration || defaultDuration);
    };

    window.JVNotify = {
        toast: function(type, message, title, options) {
            options = options || {};
            showModernNotification(type, message, title, options.duration);
        },
        loading: function(message, title) {
            const card = createToast('loading', message, title, 0);
            return {
                dismiss: () => removeToast(card),
                update: (newMsg) => {
                    const desc = card.querySelector('.noti-desc');
                    if (desc) desc.textContent = newMsg;
                }
            };
        },
        trade: function(side, asset, amount) {
            const type = side === 'buy' ? 'trade-buy' : 'trade-sell';
            const action = side === 'buy' ? 'Bought' : 'Sold';
            showModernNotification(type, `Successfully ${action.toLowerCase()} ${amount} ${asset}`, `${action} ${asset}`);
        },
        celebrate: function(title, message) {
            showModernNotification('achievement', message, title);
            if (window.confetti) {
                window.confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        },
        dismissAll: function() {
            toastQueue = [];
            if (container) {
                Array.from(container.children).forEach(child => removeToast(child, true));
            }
        }
    };

    // Backward compatibility with Toastr
    window.toastr = {
        success: function(msg, title) { showModernNotification('success', msg, title); },
        error: function(msg, title) { showModernNotification('error', msg, title); },
        info: function(msg, title) { showModernNotification('info', msg, title); },
        warning: function(msg, title) { showModernNotification('warning', msg, title); },
        options: {}
    };

    // Achievement System Port
    window.triggerAchievement = function(id, title, description) {
        const stored = localStorage.getItem('jv_achievements') || '[]';
        let achievements = [];
        try { achievements = JSON.parse(stored); } catch(e){}
        
        if (!achievements.includes(id)) {
            achievements.push(id);
            localStorage.setItem('jv_achievements', JSON.stringify(achievements));
            
            // Wait slightly so it doesn't overlap immediately with page load
            setTimeout(() => {
                JVNotify.celebrate(title || 'Achievement Unlocked', description || 'You earned a new badge!');
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
            }, 1000);
        }
    };

    // Global ESC handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            JVNotify.dismissAll();
        }
    });

    // Auto-check page visit achievements
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        if (path.includes('/dashboard')) {
            triggerAchievement('first_login', 'Welcome Aboard!', 'You accessed your dashboard for the first time.');
        } else if (path.includes('/bots') || path.includes('/bot-trades')) {
            triggerAchievement('bot_explorer', 'Bot Explorer', 'You discovered automated trading.');
        } else if (path.includes('/copy-trades') || path.includes('/pamm')) {
            triggerAchievement('social_trader', 'Social Trader', 'You checked out copy trading.');
        }
    });

})();
