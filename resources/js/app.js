import './bootstrap';
import 'nprogress/nprogress.css';

import NProgress from 'nprogress';
// import Alpine from 'alpinejs';
// import focus from '@alpinejs/focus';
import AOS from 'aos';
import Swal from 'sweetalert2';



// Tambahkan ini SEBELUM Alpine.start()
document.addEventListener('alpine:init', () => {
    Alpine.data('chatComponent', () => ({
        init() {
            this.$nextTick(() => this.scrollToBottom());

            Livewire.on('scroll-to-bottom', () => {
                this.$nextTick(() => this.scrollToBottom());
            });
        },
        scrollToBottom() {
            this.$el.scrollTop = this.$el.scrollHeight;
        }
    }));
});

// Alpine.start(); // Livewire 3 manages Alpine automatically


// ============================================
// SWEET ALERT 2
// ============================================
window.Swal = Swal;

window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

window.successToast = (message) => Toast.fire({ icon: 'success', title: message, background: '#10b981', color: '#fff', iconColor: '#fff' });
window.errorToast   = (message) => Toast.fire({ icon: 'error',   title: message, background: '#ef4444', color: '#fff', iconColor: '#fff' });
window.infoToast    = (message) => Toast.fire({ icon: 'info',    title: message });

// ============================================
// ALPINE JS
// ============================================
// window.Alpine = Alpine;
// Alpine.plugin(focus);
// Alpine.start();

// ============================================
// AOS
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({ duration: 800, easing: 'ease-in-out', once: true, offset: 100 });
});

// ============================================
// NPROGRESS
// ============================================
NProgress.configure({
    showSpinner: true,
    trickle: true,
    minimum: 0.1,
    easing: 'ease',
    speed: 400,
});

window.NProgress = NProgress;

NProgress.start();
window.addEventListener('load', () => setTimeout(() => NProgress.done(), 300));
setTimeout(() => { if (NProgress.status !== null) NProgress.done(); }, 8000);

// NProgress on navigation
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href && !link.target && link.origin === window.location.origin &&
        !link.href.includes('#') && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
        NProgress.start();
    }
});

// Ganti listener submit yang lama
document.addEventListener('submit', (e) => {
    // Abaikan form yang dihandle Livewire (wire:submit)
    if (e.target.hasAttribute('wire:submit.prevent') ||
        e.target.hasAttribute('wire:submit')) {
        return;
    }
    if (!e.defaultPrevented) NProgress.start();
});

// ============================================
// LIVEWIRE INTEGRATION
// Livewire di-load via @livewireScripts (bukan import),
// jadi harus diakses via window.Livewire setelah DOM ready
// ============================================
document.addEventListener('livewire:init', () => {
    Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
        // Abaikan NProgress jika komponen adalah chat
        if (component.name && component.name.includes('ticket-chat')) {
            return;
        }

        if (window.NProgress) window.NProgress.start();

        succeed(({ snapshot, effect }) => {
            if (window.NProgress) window.NProgress.done();
        });

        fail(() => {
            if (window.NProgress) window.NProgress.done();
            errorToast('Terjadi kesalahan koneksi/server. Silakan coba lagi.');
        });
    });
});

// ============================================
// CUSTOM EVENT LISTENERS
// ============================================
window.addEventListener('error', (event) => {
    if (event.detail?.message) errorToast(event.detail.message);
});

window.addEventListener('success', (event) => {
    if (event.detail?.message) successToast(event.detail.message);
});

window.addEventListener('livewire:error', (event) => {
    errorToast('Terjadi kesalahan pada aplikasi.');
});

// ============================================
// CHAT AUTO-SCROLL
// ============================================
window.scrollChatToBottom = (containerId) => {
    const container = document.getElementById(containerId);
    if (container) container.scrollTop = container.scrollHeight;
};

// ============================================
// WEBSOCKET STATUS INDICATOR
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const wsStatusElement = document.getElementById('ws-status');
    if (wsStatusElement && window.Echo) {
        const updateStatus = (status, color) => {
            wsStatusElement.innerHTML = status;
            wsStatusElement.style.color = color;
        };
        window.Echo.connector.pusher.connection.bind('connected',    () => updateStatus('● Online', '#10b981'));
        window.Echo.connector.pusher.connection.bind('disconnected', () => updateStatus('● Offline', '#ef4444'));
        window.Echo.connector.pusher.connection.bind('error',        () => updateStatus('● Error', '#f59e0b'));
    }
});

// ============================================
// EXPORTS — hanya export yang benar-benar ada di scope ini
// Livewire TIDAK di-export karena dia global via @livewireScripts
// ============================================
export { NProgress, Swal };
