/**
 * Ideal Mega Menu – Front-End JS
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var config   = window.immConfig || {};
        var anim     = config.animation || 'fade';
        var speed    = parseInt(config.animationSpeed, 10) || 300;
        var nav      = document.querySelector('.imm-mega-nav');

        if (!nav) return;

        // Apply animation type as data attribute for CSS hooks
        nav.setAttribute('data-animation', anim);

        // Set transition speed on dropdowns
        var dropdowns = nav.querySelectorAll('.imm-mega-dropdown');
        dropdowns.forEach(function (dd) {
            dd.style.transitionDuration = speed + 'ms';
        });

        // ─── Accessible keyboard navigation ───
        var megaItems = nav.querySelectorAll('.imm-has-mega');
        megaItems.forEach(function (item) {
            var link = item.querySelector(':scope > a');

            // Open on focus (keyboard navigation)
            link.addEventListener('focus', function () {
                closeMegas();
                item.classList.add('imm-mega-open');
            });

            // Close when focus leaves the mega item entirely
            item.addEventListener('focusout', function (e) {
                setTimeout(function () {
                    if (!item.contains(document.activeElement)) {
                        item.classList.remove('imm-mega-open');
                    }
                }, 50);
            });

            // Toggle on touch for mobile
            link.addEventListener('touchstart', function (e) {
                if (!item.classList.contains('imm-mega-open')) {
                    e.preventDefault();
                    closeMegas();
                    item.classList.add('imm-mega-open');
                }
            });
        });

        // Escape key closes megas
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMegas();
            }
        });

        // Click outside closes megas
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target)) {
                closeMegas();
            }
        });

        function closeMegas() {
            megaItems.forEach(function (item) {
                item.classList.remove('imm-mega-open');
            });
        }
    });
})();