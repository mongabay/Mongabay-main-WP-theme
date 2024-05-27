(function ($) {
    'use strict';

    $(document).ready(mbOnDocumentReady);

    function mbOnDocumentReady() {
        mbHeroSmoothScroll().init();
    }

    function mbHeroSmoothScroll() {
        var items = $('.stream-item-in-post-5 .grid-entry'),
            dY = 30;

        var ease = function (a, b, n) {
            return (1 - n) * a + n * b;
        };

        var inView = function (item, y) {
            if (window.scrollY + window.innerHeight > item.offset().top &&
                window.scrollY < item.offset().top + item.outerHeight()) {
                return true;
            }
            return false;
        };

        var itemsInView = function () {
            return items.filter(function () {
                return inView($(this));
            });
        };

        var init = function () {
            items.each(function () {
                var item = $(this);
                item.data('y', 0);
                item.data('c', Math.random());
            });

            function loop() {
                itemsInView().each(function () {
                    var item = $(this);
                    var deltaY = (item.offset().top - window.scrollY) / window.innerHeight - 1;

                    item.data('y', ease(item.data('y'), deltaY, item.data('c') * .15));
                    item.css({
                        'transform': 'translate3d(0,' + (dY * item.data('y')).toFixed(2) + '%,0)',
                    });
                });
                requestAnimationFrame(loop);
            }

            $(window).on('scroll', loop);
        };

        return {
            init: function () {
                if (items.length) init();
            }
        };
    }
})(jQuery);