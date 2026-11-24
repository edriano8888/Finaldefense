<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (defined('INSTROOM_TOAST_PARTIAL_LOADED')) return;
define('INSTROOM_TOAST_PARTIAL_LOADED', true);
$CI =& get_instance();
?>

<!-- Global Modern Floating Toast Container -->
<div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm pointer-events-none"></div>

<script>
    (function() {
        // Prevent duplicate definition if loaded multiple times
        if (window.__instroomToastSystemLoaded) return;
        window.__instroomToastSystemLoaded = true;

        /**
         * Global Modern Floating Toast Notification System
         * Matches modern floating toast design: rounded-2xl white card,
         * soft icon pill, bold title, clean body, dismiss button, bottom accent bar.
         */
        window.showToast = function(titleOrMsg, messageOrType = '', type = 'success', duration = 3500, actionUrl = null, actionText = 'Reconnect') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            let title = titleOrMsg;
            let message = messageOrType;
            let toastType = type;

            // Handle polymorphic calls:
            // 1. showToast('Message')
            if (arguments.length === 1) {
                title = 'Notification';
                message = titleOrMsg;
                toastType = 'success';
            } 
            // 2. showToast('Message', isError) where second arg is boolean
            else if (typeof messageOrType === 'boolean') {
                title = messageOrType ? 'Notice' : 'Success';
                message = titleOrMsg;
                toastType = messageOrType ? 'error' : 'success';
            }
            // 3. showToast('Success', 'Message') or showToast('Error', 'Message')
            else if (arguments.length === 2 && typeof titleOrMsg === 'string' && typeof messageOrType === 'string') {
                const lower = titleOrMsg.toLowerCase();
                if (lower === 'error' || lower === 'fail' || lower === 'failed') {
                    title = 'Notice';
                    toastType = 'error';
                } else if (lower === 'success') {
                    title = 'Success';
                    toastType = 'success';
                } else if (lower === 'info') {
                    title = 'Information';
                    toastType = 'info';
                } else {
                    title = titleOrMsg;
                    message = messageOrType;
                    toastType = 'success';
                }
            }

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto transform transition-all duration-300 ease-out translate-x-12 opacity-0 flex items-start gap-3.5 p-4 rounded-2xl bg-white/95 backdrop-blur-md shadow-xl border border-gray-100 max-w-sm w-full relative overflow-hidden';
            toast.setAttribute('role', 'alert');

            let iconHtml = '<i class="fas fa-check text-emerald-600 text-sm"></i>';
            let iconBg = 'bg-emerald-50 border-emerald-100';
            let barColor = 'bg-brand-green';

            if (toastType === 'info') {
                const isSpinning = typeof message === 'string' && (message.toLowerCase().includes('syncing') || message.toLowerCase().includes('checking') || message.toLowerCase().includes('refreshing'));
                iconHtml = `<i class="fas ${isSpinning ? 'fa-sync fa-spin' : 'fa-info'} text-blue-600 text-sm"></i>`;
                iconBg = 'bg-blue-50 border-blue-100';
                barColor = 'bg-blue-500';
            } else if (toastType === 'error') {
                iconHtml = '<i class="fas fa-exclamation text-rose-600 text-sm"></i>';
                iconBg = 'bg-rose-50 border-rose-100';
                barColor = 'bg-rose-500';
            } else if (toastType === 'warning') {
                iconHtml = '<i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>';
                iconBg = 'bg-amber-50 border-amber-100';
                barColor = 'bg-amber-500';
            }

            let actionHtml = '';
            if (actionUrl) {
                actionHtml = `
                    <div class="mt-2.5">
                        <a href="${actionUrl}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 px-3 py-1.5 rounded-xl transition-all shadow-xs">
                            ${actionText} <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                `;
            }

            // Safe HTML escaping helper
            const escapeHtml = (str) => {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            };

            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl ${iconBg} border flex items-center justify-center shrink-0 shadow-xs">
                    ${iconHtml}
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="text-xs font-extrabold text-gray-900 leading-tight">${escapeHtml(title)}</h5>
                    <p class="text-[11px] font-medium text-gray-500 leading-relaxed mt-0.5">${escapeHtml(message)}</p>
                    ${actionHtml}
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors shrink-0 -mt-1 -mr-1" onclick="this.closest('.pointer-events-auto').remove()" title="Close notification">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <div class="absolute bottom-0 left-0 right-0 h-0.5 ${barColor} opacity-70"></div>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-12', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            });

            if (duration > 0) {
                setTimeout(() => {
                    toast.classList.remove('translate-x-0', 'opacity-100');
                    toast.classList.add('translate-x-12', 'opacity-0');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 300);
                }, duration);
            }
        };

        // Gracefully wrap window.alert to render our modern floating toast
        if (!window.__originalAlert) {
            window.__originalAlert = window.alert;
            window.alert = function(msg) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Notification', String(msg), 'info', 4000);
                } else if (window.__originalAlert) {
                    window.__originalAlert(msg);
                }
            };
        }
    })();
</script>

<?php 
// Automatically capture and trigger session flashdata into modern floating toasts
$flash_success = $CI->session->flashdata('success');
$flash_error = $CI->session->flashdata('error');
$flash_info = $CI->session->flashdata('info');
$flash_warning = $CI->session->flashdata('warning');
?>
<?php if ($flash_success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.showToast) {
                    let msg = <?= json_encode((string)$flash_success) ?>;
                    let title = 'Success';
                    if (msg.toLowerCase().includes('read')) {
                        title = 'All Messages Read';
                    } else if (msg.toLowerCase().includes('sync') || msg.toLowerCase().includes('inbox')) {
                        title = 'Inbox Updated';
                    }
                    window.showToast(title, msg, 'success', 4000);
                }
            }, 100);
        });
    </script>
<?php endif; ?>

<?php if ($flash_error): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.showToast) {
                    window.showToast('Notice', <?= json_encode((string)$flash_error) ?>, 'error', 5000);
                }
            }, 100);
        });
    </script>
<?php endif; ?>

<?php if ($flash_info): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.showToast) {
                    window.showToast('Information', <?= json_encode((string)$flash_info) ?>, 'info', 4000);
                }
            }, 100);
        });
    </script>
<?php endif; ?>

<?php if ($flash_warning): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.showToast) {
                    window.showToast('Warning', <?= json_encode((string)$flash_warning) ?>, 'warning', 4500);
                }
            }, 100);
        });
    </script>
<?php endif; ?>
