/**
 * TalentSync AI — Theme Manager (light-only build)
 * Dark/light toggle is disabled. Always light.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.documentElement.setAttribute('data-theme', 'light');
    document.querySelectorAll('[data-theme-icon]').forEach(function (el) {
        el.textContent = 'dark_mode';
    });

    var header = document.getElementById('site-header');
    if (!header) {
        return;
    }

    var ticking = false;
    var lastState = null;
    var threshold = 12;

    function applyHeaderState() {
        var isScrolled = window.scrollY > threshold;
        if (isScrolled === lastState) {
            return;
        }
        lastState = isScrolled;
        header.classList.toggle('site-header--scrolled', isScrolled);
    }

    function onScroll() {
        if (ticking) {
            return;
        }
        ticking = true;
        window.requestAnimationFrame(function () {
            applyHeaderState();
            ticking = false;
        });
    }

    applyHeaderState();
    window.addEventListener('scroll', onScroll, { passive: true });
});

function applyTheme() {}
function toggleTheme() {}
