(function ($) {
   "use strict";

   jQuery(document).ready(function ($) {




       $('.menu-open , .offcanvas-overlay').click(function () {

           $('.offcanvas-area-main , .offcanvas-overlay').addClass('active');

       });
       $('.off-menu-close , .offcanvas-overlay').click(function () {

           $('.offcanvas-area-main , .offcanvas-overlay').removeClass('active');

       });

       $('.secondary__navbar__menu__open , .offcanvas-overlay').click(function () {

           $('.offcanvas-area , .offcanvas-overlay').addClass('active');

       });
       $('.secondary__navbar__offcanvas__menu__close , .offcanvas-overlay').click(function () {

           $('.offcanvas-area , .offcanvas-overlay').removeClass('active');

       });



       $(".market__slider__wrapper").owlCarousel({
           items: 4,
           nav: true,
           dot: true,
           loop: true,
           margin: 20,
           autoplay: false,
           autoplayTimeout: 3000,
           smartSpeed: 1000,
           responsiveClass: true,
           navText: ["<i class='far fa-chevron-left'></i>", "<i class='far fa-chevron-right'></i>"],
           responsive: {
               0: {
                   items: 1,
               },
               575: {
                   items: 2,
                   margin: 15,
                   stagePadding: 5
               },
               750: {
                   items: 2,
                   margin: 7,
               },
               768: {
                   items: 2,
               },
               991: {
                   items: 3,
                   margin: 12,
               },
               1200: {
                   items: 3,
                   margin: 14,
               },
               1300: {
                   items: 4,
                   margin: 40
               }
           }


       });


       
       $(".provider__slider__wrapper").owlCarousel({
           items: 6,
           nav: true,
           dot: false,
           loop: true,
           margin: 20,
           autoplay: false,
           autoplayTimeout: 3000,
           smartSpeed: 1000,
           responsiveClass: true,
           navText: ["<i class='far fa-chevron-left'></i>", "<i class='far fa-chevron-right'></i>"],
           responsive: {
               0: {
                   items: 2,
               },
               575: {
                   items: 2,
                   margin: 7,
               },
               750: {
                   items: 3,
                   margin: 7,
               },
               768: {
                   items: 3,
               },
               991: {
                   items: 3,
                   margin: 12,
               },
               1200: {
                   items: 6,
                   margin: 20,
               },
               1300: {
                   items: 6,
                   margin: 30
               }
           }


       });


   });

   jQuery(window).load(function () {


   });


}(jQuery));

// Fixed to TOP Header
$(window).on('scroll', function () {
   // Header Sticky JS
   if ($(this).scrollTop() > 150) {
       $('.header').addClass("sticky-top");
   }
   else {
       $('.header').removeClass("sticky-top");
   };


});