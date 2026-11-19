/*
 * kern-nav.js – barrierefreie Disclosure-Navigation für kern_contao
 *
 * Muster: WAI-ARIA Authoring Practices „Disclosure Navigation Menu“
 * https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/examples/disclosure-navigation/
 *
 * - Ohne dieses Script ist die Navigation vollständig ausgeklappt.
 * - Menü-Button (nur unterhalb des Breakpoints sichtbar) blendet die Navigation ein/aus.
 * - Unterebenen werden über Buttons mit aria-expanded auf-/zugeklappt.
 * - Escape schließt die geöffnete Ebene und setzt den Fokus auf ihren Button zurück.
 * - Desktop: Verlässt der Fokus oder ein Klick die Navigation, schließen die Untermenüs.
 * - Mobil: Unterebenen mit der aktuellen Seite sind beim Laden geöffnet.
 *
 * Breakpoint: CSS-Variable --kern-nav-breakpoint am <nav> (Standard 992px).
 * Das Script setzt kern-nav--js und je nach Breite kern-nav--desktop bzw.
 * kern-nav--mobile; das gesamte Layout in kern-contao.css hängt an diesen Klassen.
 */
(() => {
    'use strict';

    const OPEN_CLASS = 'kern-nav--open';

    const setExpanded = (button, expanded) => {
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    };

    const isExpanded = (button) => button.getAttribute('aria-expanded') === 'true';

    const init = (nav) => {
        if (nav.classList.contains('kern-nav--js')) {
            return;
        }

        const breakpoint = getComputedStyle(nav).getPropertyValue('--kern-nav-breakpoint').trim() || '992px';
        const desktop = window.matchMedia(`(min-width: ${breakpoint})`);
        const toggle = nav.querySelector('.kern-nav__toggle');
        const subtoggles = [...nav.querySelectorAll('.kern-nav__subtoggle')];

        // Buttons der obersten Ebene (Desktop: Dropdowns)
        const topLevel = subtoggles.filter((b) => !b.closest('.kern-nav__submenu'));

        const closeSubmenus = (except = null) => {
            topLevel.forEach((b) => {
                if (b !== except) {
                    setExpanded(b, false);
                }
            });
        };

        const closeMenu = () => {
            if (toggle) {
                setExpanded(toggle, false);
            }
            nav.classList.remove(OPEN_CLASS);
        };

        const applyLayout = () => {
            nav.classList.toggle('kern-nav--desktop', desktop.matches);
            nav.classList.toggle('kern-nav--mobile', !desktop.matches);

            if (desktop.matches) {
                // Desktop: Menü immer sichtbar, Dropdowns geschlossen
                closeMenu();
                subtoggles.forEach((b) => setExpanded(b, false));
            } else {
                // Mobil: Pfad zur aktuellen Seite geöffnet
                subtoggles.forEach((b) => {
                    const item = b.closest('.kern-nav__item');
                    setExpanded(b, !!item && (item.classList.contains('trail') || !!item.querySelector('[aria-current="page"]')));
                });
            }
        };

        if (toggle) {
            toggle.hidden = false;
            toggle.addEventListener('click', () => {
                const expanded = !isExpanded(toggle);
                setExpanded(toggle, expanded);
                nav.classList.toggle(OPEN_CLASS, expanded);
            });
        }

        subtoggles.forEach((button) => {
            button.hidden = false;
            button.addEventListener('click', () => {
                const expanded = !isExpanded(button);
                if (desktop.matches && topLevel.includes(button)) {
                    closeSubmenus(button);
                }
                setExpanded(button, expanded);
            });
        });

        // Escape: innerste offene Ebene schließen, Fokus zurück auf deren Button
        nav.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            const submenu = event.target.closest('.kern-nav__submenu');
            const button = submenu && nav.querySelector(`[aria-controls="${submenu.id}"]`);

            if (button && isExpanded(button)) {
                setExpanded(button, false);
                button.focus();
                event.preventDefault();
            } else if (toggle && isExpanded(toggle) && !desktop.matches) {
                closeMenu();
                toggle.focus();
                event.preventDefault();
            }
        });

        // Desktop: Dropdown schließen, wenn der Fokus die Navigation verlässt
        nav.addEventListener('focusout', (event) => {
            if (desktop.matches && event.relatedTarget && !nav.contains(event.relatedTarget)) {
                closeSubmenus();
            }
        });

        // Desktop: Klick außerhalb schließt Dropdowns
        document.addEventListener('click', (event) => {
            if (desktop.matches && !nav.contains(event.target)) {
                closeSubmenus();
            }
        });

        desktop.addEventListener('change', applyLayout);
        applyLayout();

        nav.classList.add('kern-nav--js');
    };

    const initAll = () => document.querySelectorAll('[data-kern-nav]').forEach(init);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
