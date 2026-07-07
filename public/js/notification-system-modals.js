(function() {
    'use strict';

    let openModals = [];
    let previousFocus = null;

    window.JVModal = {
        show: function(modalId, options = {}) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            previousFocus = document.activeElement;
            
            // Clean up old classes
            modal.classList.remove('jv-modal-exit');
            
            // Show modal
            modal.style.display = 'flex';
            
            // Force reflow
            void modal.offsetWidth;

            openModals.push({ id: modalId, static: options.static || modal.getAttribute('data-static') === 'true' });
            
            // Trap focus
            trapFocus(modal);
            
            // Trigger confetti if requested
            if (options.celebrate && window.confetti) {
                window.confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
            }
        },

        hide: function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.add('jv-modal-exit');
            
            setTimeout(() => {
                modal.style.display = 'none';
                modal.classList.remove('jv-modal-exit');
                
                // Return focus
                if (previousFocus) {
                    previousFocus.focus();
                }
            }, 300);

            openModals = openModals.filter(m => m.id !== modalId);
        },

        create: function(options) {
            const id = options.id || 'jv-modal-' + Math.random().toString(36).substr(2, 9);
            
            let html = `
                <div class="jv-modal-backdrop" onclick="${options.static ? '' : `JVModal.hide('${id}')`}"></div>
                <div class="jv-modal-content" role="dialog" aria-modal="true" aria-labelledby="${id}-title">
                    <button class="jv-modal-close" onclick="JVModal.hide('${id}')" aria-label="Close">
                        <i class="ri-close-line" style="font-size: 20px;"></i>
                    </button>
                    <div class="jv-modal-body">
            `;

            if (options.icon) {
                html += `<div class="jv-modal-icon" style="color: ${options.iconColor || '#fff'}">${options.icon}</div>`;
            }

            if (options.badge) {
                html += `<div style="text-align:center"><span class="jv-modal-badge">${options.badge}</span></div>`;
            }

            if (options.title) {
                const titleClass = options.titleGradient ? 'jv-modal-title jv-modal-title-gradient' : 'jv-modal-title';
                html += `<h2 id="${id}-title" class="${titleClass}">${options.title}</h2>`;
            }

            if (options.description) {
                html += `<p class="jv-modal-desc">${options.description}</p>`;
            }

            if (options.features && options.features.length > 0) {
                html += `<ul class="jv-modal-features">`;
                options.features.forEach(f => {
                    html += `<li><i class="ri-checkbox-circle-fill"></i> ${f}</li>`;
                });
                html += `</ul>`;
            }

            if (options.ctaText) {
                const btnClass = options.ctaClass || 'jv-modal-cta jv-modal-cta-primary';
                const onClick = options.onCta ? options.onCta : `JVModal.hide('${id}')`;
                const href = options.ctaUrl ? `href="${options.ctaUrl}"` : '';
                const tag = options.ctaUrl ? 'a' : 'button';
                
                html += `<${tag} ${href} class="${btnClass}" onclick="${options.ctaUrl ? '' : onClick}">${options.ctaText}</${tag}>`;
            }

            if (options.ghostText) {
                const onClick = options.onGhost ? options.onGhost : `JVModal.hide('${id}')`;
                html += `<button class="jv-modal-ghost" onclick="${onClick}">${options.ghostText}</button>`;
            }

            html += `</div></div>`;

            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = `jv-modal jv-modal-${options.size || 'md'}`;
            wrapper.style.display = 'none';
            wrapper.innerHTML = html;
            
            document.body.appendChild(wrapper);
            
            if (options.autoShow) {
                this.show(id, options);
            }
            
            return id;
        },
        
        celebrate: function(modalId) {
            if (window.confetti) {
                window.confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
            }
        }
    };

    function trapFocus(element) {
        const focusableElements = element.querySelectorAll('a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select, [tabindex]:not([tabindex="-1"])');
        if (focusableElements.length === 0) return;

        const first = focusableElements[0];
        const last = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === first) {
                        last.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === last) {
                        first.focus();
                        e.preventDefault();
                    }
                }
            }
        });

        // Focus first element on load
        setTimeout(() => first.focus(), 100);
    }

    // Global ESC handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // JVModals
            if (openModals.length > 0) {
                const topmost = openModals[openModals.length - 1];
                if (!topmost.static) {
                    JVModal.hide(topmost.id);
                }
            }
            // Drawers
            if (window.JVDrawer && typeof window.JVDrawer.hideAll === 'function') {
                window.JVDrawer.hideAll();
            }
        }
    });

    // Handle clicks outside Bootstrap modals (if needed for custom logic)
    // Bootstrap handles its own backdrop clicks, but we might want to hook into it
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
            jQuery(document).on('show.bs.modal', function(e) {
                // Add blur backdrop class to body so it hits the bootstrap backdrop
                document.body.classList.add('modal-open');
            });
            jQuery(document).on('hide.bs.modal', function(e) {
                document.body.classList.remove('modal-open');
            });
        }
    });

})();
