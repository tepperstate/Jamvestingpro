document.addEventListener('DOMContentLoaded', () => {
    // Bottom Sheet Logic
    const bottomSheets = document.querySelectorAll('.bottom-sheet');
    const bottomSheetBackdrop = document.querySelector('.bottom-sheet-backdrop');
    
    // Function to open sheet
    window.openSheet = (sheetId) => {
        const sheet = document.getElementById(sheetId);
        if (sheet && bottomSheetBackdrop) {
            sheet.classList.add('active');
            bottomSheetBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    };

    // Function to close sheet
    window.closeSheet = () => {
        const activeSheet = document.querySelector('.bottom-sheet.active');
        if (activeSheet && bottomSheetBackdrop) {
            activeSheet.classList.remove('active');
            bottomSheetBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    if (bottomSheetBackdrop) {
        bottomSheetBackdrop.addEventListener('click', window.closeSheet);
    }

    // Swipe down to close sheet
    let startY = 0;
    let currentY = 0;
    
    bottomSheets.forEach(sheet => {
        const handle = sheet.querySelector('.sheet-handle');
        if (handle) {
            handle.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
            }, { passive: true });
            
            handle.addEventListener('touchmove', (e) => {
                currentY = e.touches[0].clientY;
                const diff = currentY - startY;
                if (diff > 0) {
                    sheet.style.transform = `translateY(${diff}px)`;
                }
            }, { passive: true });
            
            handle.addEventListener('touchend', () => {
                const diff = currentY - startY;
                sheet.style.transform = ''; // reset to CSS transform
                if (diff > 100) {
                    window.closeSheet();
                }
            });
        }
    });

    // Swipeable List Cards
    const swipeableCards = document.querySelectorAll('.swipeable-card');
    swipeableCards.forEach(card => {
        let cardStartX = 0;
        let cardCurrentX = 0;
        const innerContent = card.querySelector('.swipe-content');
        if(!innerContent) return;

        innerContent.addEventListener('touchstart', (e) => {
            cardStartX = e.touches[0].clientX;
            innerContent.style.transition = 'none';
        }, { passive: true });

        innerContent.addEventListener('touchmove', (e) => {
            cardCurrentX = e.touches[0].clientX;
            const diffX = cardCurrentX - cardStartX;
            
            // Limit swipe distance
            if (diffX > -100 && diffX < 100) {
                innerContent.style.transform = `translateX(${diffX}px)`;
            }
        }, { passive: true });

        innerContent.addEventListener('touchend', () => {
            innerContent.style.transition = 'transform 200ms ease';
            const diffX = cardCurrentX - cardStartX;
            
            if (diffX < -50) {
                // Swipe Left - show actions on right
                innerContent.style.transform = `translateX(-80px)`;
            } else if (diffX > 50) {
                // Swipe Right - show actions on left
                innerContent.style.transform = `translateX(80px)`;
            } else {
                // Snap back
                innerContent.style.transform = `translateX(0px)`;
            }
        });
    });

    // Pull to Refresh (Basic Implementation)
    let pStartY = 0;
    const ptrEl = document.querySelector('.pull-to-refresh');
    
    if (ptrEl) {
        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                pStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (window.scrollY === 0 && pStartY > 0) {
                const diff = e.touches[0].clientY - pStartY;
                if (diff > 0 && diff < 100) {
                    ptrEl.style.height = `${diff}px`;
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (pStartY > 0) {
                pStartY = 0;
                ptrEl.style.height = '0px';
                // Trigger refresh if pulled enough
                // window.location.reload();
            }
        });
    }
});
