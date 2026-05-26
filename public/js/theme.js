/**
 * Elements HR — Theme Manager (light-only build)
 * Dark/light toggle is disabled. Always light.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.documentElement.setAttribute('data-theme', 'light');
    document.querySelectorAll('[data-theme-icon]').forEach(function (el) {
        el.textContent = 'dark_mode';
    });
});

function applyTheme() {}
function toggleTheme() {}
