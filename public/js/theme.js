/**
 * Elements HR — Theme Manager
 * Toggles data-theme="dark" on <html>. Light is default.
 * Compatible with existing localStorage key "elements-theme".
 */

var THEME_KEY = 'elements-theme';

/**
 * Apply a theme ('light' | 'dark') immediately.
 * Updates the attribute, persists to localStorage, and syncs icons.
 */
function applyTheme(theme) {
    var html = document.documentElement;
    html.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
    _syncThemeIcons(theme === 'dark');
}

/**
 * Toggle between light and dark.
 * Called by any onclick="toggleTheme()" button on any page.
 */
function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme');
    applyTheme(current === 'dark' ? 'light' : 'dark');
}

/**
 * Update all [data-theme-icon] elements to show the correct icon.
 * Icons show what clicking the button WILL do (opposite of current).
 */
function _syncThemeIcons(isDark) {
    document.querySelectorAll('[data-theme-icon]').forEach(function (el) {
        el.textContent = isDark ? 'light_mode' : 'dark_mode';
    });
}

// Sync icons once DOM is ready (the flash guard already set data-theme)
document.addEventListener('DOMContentLoaded', function () {
    var saved = localStorage.getItem(THEME_KEY) || 'light';
    _syncThemeIcons(saved === 'dark');
});
