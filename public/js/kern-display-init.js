/*
 * kern-display-init.js – wendet gespeicherte Darstellungs-Einstellungen an,
 * bevor die Seite gezeichnet wird (blockierend im <head>, sehr klein).
 * Gegenstück: kern-display.js
 */
(() => {
    try {
        const prefs = JSON.parse(localStorage.getItem('kern-display') || '{}');
        const root = document.documentElement;

        if (prefs.theme === 'light' || prefs.theme === 'dark') {
            root.setAttribute('data-kern-theme', prefs.theme);
        }
        if (prefs.font === 'atkinson') {
            root.setAttribute('data-kern-font', 'atkinson');
        }
        if (prefs.spacing === 'wide') {
            root.setAttribute('data-kern-spacing', 'wide');
        }
    } catch (e) {
        // Kein Zugriff auf localStorage (z. B. blockiert) – Standarddarstellung
    }
})();
