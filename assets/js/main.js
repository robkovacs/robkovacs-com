((function () {
    'use strict';

    /* Utility functions */
    var utils = {
        removeClass: function (el, className) {
            if (el.classList) {
                el.classList.remove(className);
            } else {
                el.class = el.class.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
            }
        },
        addClass: function (el, className) {
            if (el.classList) {
                el.classList.add(className);
            } else {
                el.class += ' ' + className;
            }
        }
    };

    /* Make images on project pages show up all fancy-like */
    if (document.querySelectorAll("figure").length > 0) {
        var sr = new ScrollReveal({
            origin: 'bottom',
            duration: 750,
            viewFactor: 0.5,
            scale: 0.85
        });
        sr.reveal("figure");
    }

    /*  Open the menu on mobile when you click the [ MENU ] button */
    document.querySelector('.header__button button').addEventListener('click', function () {
        this.blur();
        this.setAttribute('aria-expanded', true);
        document.querySelector('.header__menu button').setAttribute('aria-expanded', true);
        utils.addClass(document.querySelector('html'), 'js--header__menu--open');
    });

    /*  Close the menu on mobile when you click the X button */
    document.querySelector('.header__menu button').addEventListener('click', function () {
        this.setAttribute('aria-expanded', false);
        document.querySelector('.header__button button').setAttribute('aria-expanded', false);
        utils.removeClass(document.querySelector('html'), 'js--header__menu--open');
    });

    /* When the screen gets too big, and the mobile menu disappears,
    remove the class that says it's open */
    window.addEventListener('resize', function () {
        if (window.matchMedia('(min-width: 60rem)').matches) {
            utils.removeClass(document.querySelector('html'), 'js--header__menu--open');
        }
    });
})());
