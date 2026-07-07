(function() {
    'use strict';

    let openDrawers = [];

    window.JVDrawer = {
        show: function(drawerId) {
            const drawer = document.getElementById('jv-drawer-' + drawerId);
            const backdrop = document.getElementById('jv-drawer-backdrop-' + drawerId);
            if (!drawer || !backdrop) return;

            // Remove exit classes if any
            drawer.classList.remove('jv-drawer-exit');
            
            // Determine position based on screen width
            const isMobile = window.innerWidth < 768;
            drawer.setAttribute('data-position', isMobile ? 'bottom' : 'right');

            backdrop.style.display = 'block';
            drawer.style.display = 'block';

            // Lock body scroll
            document.body.style.overflow = 'hidden';

            openDrawers.push(drawerId);

            // Handle mobile drag to dismiss
            if (isMobile) {
                this._initMobileDrag(drawerId);
            }
        },

        hide: function(drawerId) {
            const drawer = document.getElementById('jv-drawer-' + drawerId);
            const backdrop = document.getElementById('jv-drawer-backdrop-' + drawerId);
            if (!drawer || !backdrop) return;

            drawer.classList.add('jv-drawer-exit');
            backdrop.style.animation = 'jv-fade-out 0.3s forwards';

            setTimeout(() => {
                drawer.style.display = 'none';
                backdrop.style.display = 'none';
                drawer.classList.remove('jv-drawer-exit');
                backdrop.style.animation = '';
                drawer.style.transform = ''; // reset any drag transforms
                
                openDrawers = openDrawers.filter(id => id !== drawerId);
                
                // Unlock body scroll if no other drawers open
                if (openDrawers.length === 0 && !document.body.classList.contains('modal-open')) {
                    document.body.style.overflow = '';
                }
            }, 300);
        },
        
        hideAll: function() {
            if (openDrawers.length > 0) {
                this.hide(openDrawers[openDrawers.length - 1]);
            }
        },

        create: function(options) {
            const id = options.id || 'drawer-' + Math.random().toString(36).substr(2, 9);
            
            let html = \`
                <div class="jv-drawer-backdrop" id="jv-drawer-backdrop-\${id}" style="display:none;" onclick="JVDrawer.hide('\${id}')"></div>
                <div class="jv-drawer" id="jv-drawer-\${id}" role="dialog" aria-modal="true" aria-labelledby="jv-drawer-title-\${id}" data-position="\${options.position || 'right'}" data-width="\${options.width || '480px'}" style="display:none; \${options.position !== 'bottom' ? \`width: \${options.width || '480px'};\` : ''}">
                    <div class="jv-drawer-handle d-md-none"></div>
                    
                    <div class="jv-drawer-header">
                        <h3 class="jv-drawer-title" id="jv-drawer-title-\${id}">\${options.title}</h3>
                        <button class="jv-drawer-close" onclick="JVDrawer.hide('\${id}')" aria-label="Close drawer">
                            <i class="ri-close-line" style="font-size: 20px;"></i>
                        </button>
                    </div>
                    
                    <div class="jv-drawer-body">
                        \${options.content || ''}
                    </div>
                </div>
            \`;

            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            
            // Append nodes to body
            while (wrapper.firstChild) {
                document.body.appendChild(wrapper.firstChild);
            }
            
            if (options.autoShow) {
                this.show(id);
            }
            
            return id;
        },

        _initMobileDrag: function(drawerId) {
            const drawer = document.getElementById('jv-drawer-' + drawerId);
            const handle = drawer.querySelector('.jv-drawer-handle');
            if (!handle) return;

            let startY = 0;
            let currentY = 0;
            let isDragging = false;
            
            const onTouchStart = (e) => {
                startY = e.touches[0].clientY;
                isDragging = true;
                drawer.style.transition = 'none';
            };
            
            const onTouchMove = (e) => {
                if (!isDragging) return;
                currentY = e.touches[0].clientY - startY;
                
                // Only allow dragging down
                if (currentY > 0) {
                    drawer.style.transform = \`translateY(\${currentY}px)\`;
                } else {
                    drawer.style.transform = 'translateY(0)';
                }
            };
            
            const onTouchEnd = () => {
                if (!isDragging) return;
                isDragging = false;
                drawer.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1)';
                
                // If dragged down enough, close it
                if (currentY > 100) {
                    this.hide(drawerId);
                } else {
                    // Snap back
                    drawer.style.transform = 'translateY(0)';
                }
                
                // Cleanup
                handle.removeEventListener('touchstart', onTouchStart);
                document.removeEventListener('touchmove', onTouchMove);
                document.removeEventListener('touchend', onTouchEnd);
            };

            handle.addEventListener('touchstart', onTouchStart, { passive: true });
            document.addEventListener('touchmove', onTouchMove, { passive: true });
            document.addEventListener('touchend', onTouchEnd);
        }
    };

})();
