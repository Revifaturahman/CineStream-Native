/**
 * CineStream - Custom JavaScript
 */

$(document).ready(function () {
    // 1. Inisialisasi Owl Carousel untuk Trending Movies
    var $trendingCarousel = $('.trending-carousel');
    if ($trendingCarousel.length) {
        $trendingCarousel.owlCarousel({
            loop: true,
            margin: 16,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            smartSpeed: 600,
            navText: [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ],
            responsive: {
                0: {
                    items: 2,
                    margin: 10
                },
                576: {
                    items: 3,
                    margin: 12
                },
                768: {
                    items: 4,
                    margin: 14
                },
                992: {
                    items: 5,
                    margin: 16
                },
                1200: {
                    items: 6,
                    margin: 18
                }
            }
        });
    }

    // 2. Efek Scroll pada Header / Navbar
    function checkNavbarScroll() {
        if ($(window).scrollTop() > 30) {
            $('.header-navbar').addClass('scrolled');
        } else {
            $('.header-navbar').removeClass('scrolled');
        }
    }

    // Jalankan saat load dan saat scroll
    checkNavbarScroll();
    $(window).on('scroll', function () {
        checkNavbarScroll();
    });

    // 3. Smooth Scrolling untuk Tombol Nonton Sekarang & Anchor Links
    $(document).on('click', 'a[href^="#"]', function (e) {
        var targetId = $(this).attr('href');
        if (targetId && targetId !== '#' && $(targetId).length) {
            e.preventDefault();
            var offsetTop = $(targetId).offset().top - 80;
            $('html, body').stop().animate({
                scrollTop: offsetTop
            }, 600);
        }
    });

    // 4. Server Switcher pada Pemutar Video
    $(document).on('click', '.server-btn', function () {
        var newSource = $(this).data('src');
        if (newSource) {
            $('#videoPlayerFrame').attr('src', newSource);
            $('.server-btn').removeClass('btn-danger active').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('btn-danger active');
        }
    });
});

