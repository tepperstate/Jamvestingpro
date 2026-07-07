(function() {
    'use strict';

    // Move all Bootstrap modals to body on load to prevent z-index issues
    $(document).ready(function() {
        $('.modal').appendTo('body');
    });

    // Premium SweetAlert2 Styling injected dynamically
    const injectPremiumSwalCSS = () => {
        if (document.getElementById('jv-swal-premium-css')) return;
        const style = document.createElement('style');
        style.id = 'jv-swal-premium-css';
        style.innerHTML = \`
            .jv-swal-popup {
                background: linear-gradient(145deg, #0d0d12, #09090b) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: 20px !important;
                box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.05), 0 25px 80px rgba(0, 0, 0, 0.6) !important;
            }
            .jv-swal-title {
                color: #fff !important;
                font-family: 'Outfit', sans-serif !important;
                font-weight: 700 !important;
            }
            .jv-swal-content {
                color: #a1a1aa !important;
                font-family: 'Inter', sans-serif !important;
            }
            .jv-swal-confirm {
                background: linear-gradient(135deg, #ff3333, #6366f1) !important;
                border: none !important;
                border-radius: 12px !important;
                padding: 12px 24px !important;
                font-weight: 600 !important;
                letter-spacing: 0.5px !important;
                transition: transform 0.2s !important;
            }
            .jv-swal-confirm:hover {
                transform: scale(1.02) !important;
            }
            .jv-swal-cancel {
                background: transparent !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                color: #e4e4e7 !important;
                border-radius: 12px !important;
                padding: 12px 24px !important;
                font-weight: 600 !important;
            }
            .jv-swal-cancel:hover {
                background: rgba(255, 255, 255, 0.05) !important;
            }
            .swal2-icon.swal2-warning { border-color: #f59e0b !important; color: #f59e0b !important; filter: drop-shadow(0 0 8px rgba(245,158,11,0.5)) !important; }
            .swal2-icon.swal2-error { border-color: #ef4444 !important; color: #ef4444 !important; filter: drop-shadow(0 0 8px rgba(239,68,68,0.5)) !important; }
            .swal2-icon.swal2-success { border-color: #10b981 !important; color: #10b981 !important; filter: drop-shadow(0 0 8px rgba(16,185,129,0.5)) !important; }
            .swal2-icon.swal2-info { border-color: #3b82f6 !important; color: #3b82f6 !important; filter: drop-shadow(0 0 8px rgba(59,130,246,0.5)) !important; }
            
            /* Remove blur and overlay from SweetAlert backdrop */
            .swal2-container.swal2-backdrop-show {
                background: transparent !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                pointer-events: none !important;
            }
        \`;
        document.head.appendChild(style);
    };

    // Generic Confirm Action
    $(document).on('click', '.confirm-action, [data-confirm]', function(e) {
        e.preventDefault();
        
        injectPremiumSwalCSS();
        
        const el = $(this);
        const url = el.attr('href');
        const form = el.closest('form');
        const text = el.data('confirm') || 'Are you sure you want to perform this action?';
        const title = el.data('title') || 'Confirm Action';
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'jv-swal-popup',
                title: 'jv-swal-title',
                htmlContainer: 'jv-swal-content',
                confirmButton: 'jv-swal-confirm',
                cancelButton: 'jv-swal-cancel'
            },
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster' // we can just rely on swal's default or our jv-modal-enter if we mapped it, but swal's zoomIn is fine
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (url && url !== '#' && url !== 'javascript:void(0)') {
                    window.location.href = url;
                } else if (form.length > 0) {
                    form.submit();
                }
            }
        });
    });

    // AJAX Modal Form Handler (Preserved from original)
    window.handleAjaxModal = function(formSelector, modalId, successMessage) {
        $(document).on('submit', formSelector, function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = form.find('button[type="submit"]');
            const originalText = btn.html();
            
            btn.html('<i class="ri-loader-4-line ri-spin"></i> Processing...').prop('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method') || 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    btn.html(originalText).prop('disabled', false);
                    $('#' + modalId).modal('hide');
                    
                    if (window.JVNotify) {
                        window.JVNotify.toast('success', successMessage || (response.message ? response.message : 'Action completed successfully'));
                    } else if (window.toastr) {
                        toastr.success(successMessage || response.message);
                    }
                    
                    setTimeout(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    let errMsg = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        errMsg = xhr.responseJSON.errors[firstKey][0];
                    }
                    
                    if (window.JVNotify) {
                        window.JVNotify.toast('error', errMsg);
                    } else if (window.toastr) {
                        toastr.error(errMsg);
                    }
                }
            });
        });
    };

})();
