/*
 * kern-display.js – Modul „Darstellung anpassen“ (kern_contao)
 *
 * - Disclosure-Button (aria-expanded) blendet das Panel ein und aus.
 * - Radiobuttons wirken sofort; die Auswahl wird in localStorage gespeichert
 *   und von kern-display-init.js beim nächsten Aufruf vor dem Zeichnen angewendet.
 * - Änderungen werden über eine Live-Region angesagt.
 * - Escape schließt das Panel und setzt den Fokus auf den Button zurück.
 */
(() => {
    'use strict';

    const KEY = 'kern-display';
    const DEFAULTS = { font: 'default', theme: 'system', spacing: 'default' };
    const root = document.documentElement;

    const load = () => {
        try {
            return { ...DEFAULTS, ...JSON.parse(localStorage.getItem(KEY) || '{}') };
        } catch (e) {
            return { ...DEFAULTS };
        }
    };

    const save = (prefs) => {
        try {
            const changed = Object.fromEntries(Object.entries(prefs).filter(([k, v]) => DEFAULTS[k] !== v));
            if (Object.keys(changed).length) {
                localStorage.setItem(KEY, JSON.stringify(changed));
            } else {
                localStorage.removeItem(KEY);
            }
        } catch (e) {
            // Speichern nicht möglich – Einstellung gilt nur für diese Seite
        }
    };

    const apply = (prefs) => {
        const set = (name, value) => (value ? root.setAttribute(name, value) : root.removeAttribute(name));
        set('data-kern-theme', prefs.theme === 'light' || prefs.theme === 'dark' ? prefs.theme : null);
        set('data-kern-font', prefs.font === 'atkinson' ? 'atkinson' : null);
        set('data-kern-spacing', prefs.spacing === 'wide' ? 'wide' : null);
    };

    const init = (module) => {
        const toggle = module.querySelector('.kern-display__toggle');
        const panel = module.querySelector('.kern-display__panel');
        const status = module.querySelector('.kern-display__status');
        const radios = [...module.querySelectorAll('[data-kern-display-setting]')];
        const reset = module.querySelector('[data-kern-display-reset]');

        if (!toggle || !panel) {
            return;
        }

        const sync = (prefs) => {
            radios.forEach((radio) => {
                radio.checked = prefs[radio.dataset.kernDisplaySetting] === radio.value;
            });
        };

        const announce = (text) => {
            if (status) {
                status.textContent = '';
                window.setTimeout(() => { status.textContent = text; }, 50);
            }
        };

        const setOpen = (open) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            panel.hidden = !open;
        };

        let prefs = load();
        sync(prefs);

        toggle.addEventListener('click', () => setOpen(panel.hidden));

        radios.forEach((radio) => {
            radio.addEventListener('change', () => {
                prefs = { ...prefs, [radio.dataset.kernDisplaySetting]: radio.value };
                apply(prefs);
                save(prefs);
                // Andere Instanzen auf der Seite angleichen
                document.dispatchEvent(new CustomEvent('kern-display:change', { detail: prefs }));
                const label = module.querySelector(`label[for="${radio.id}"]`);
                announce(label ? label.textContent.trim() : '');
            });
        });

        if (reset) {
            reset.addEventListener('click', () => {
                prefs = { ...DEFAULTS };
                apply(prefs);
                save(prefs);
                document.dispatchEvent(new CustomEvent('kern-display:change', { detail: prefs }));
                announce(reset.textContent.trim());
            });
        }

        document.addEventListener('kern-display:change', (event) => sync(event.detail));

        module.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.hidden) {
                setOpen(false);
                toggle.focus();
                event.preventDefault();
            }
        });

        module.hidden = false;
    };

    const initAll = () => document.querySelectorAll('[data-kern-display]').forEach(init);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
