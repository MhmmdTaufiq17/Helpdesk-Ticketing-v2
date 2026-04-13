// resources/js/public-session.js
let lastActivity = Date.now();
const SESSION_TIMEOUT = 60 * 5 * 1000;
// const SESSION_TIMEOUT = 10 * 1000; // 5 menit
let timeoutId;

function resetTimer() {
    lastActivity = Date.now();
    clearTimeout(timeoutId);
    timeoutId = setTimeout(checkTimeout, SESSION_TIMEOUT);
}

function checkTimeout() {
    const currentPath = window.location.pathname;
    const inactiveTime = Date.now() - lastActivity;

    if (inactiveTime >= SESSION_TIMEOUT) {
        // Halaman Buat Tiket (Home/Create)
        if (currentPath === '/' || currentPath === '/home' || currentPath === '/tiket/create' || currentPath === '/') {
            clearAllForms();
            // HAPUS TOAST DI SINI - TIDAK ADA NOTIFIKASI
        }
        // Halaman Status Tiket (Track/Show)
        else if (currentPath.includes('/tiket/') || currentPath.includes('/track')) {
            window.location.href = '/';
        }
    }
}

function clearAllForms() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.reset();

        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.type === 'text' || input.type === 'email' || input.type === 'tel' || input.type === 'number' || input.type === 'search') {
                input.value = '';
            }
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            }
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
    });

    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.value = '';
    });

    const fileNotif = document.getElementById('fileNotification');
    if (fileNotif) {
        fileNotif.style.display = 'none';
    }
}

// Event listeners untuk aktivitas user
const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
events.forEach(event => {
    document.addEventListener(event, resetTimer);
});

// Start timer saat halaman dimuat
document.addEventListener('DOMContentLoaded', resetTimer);
