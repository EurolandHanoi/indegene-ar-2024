$(document).ready(function(){
    collectMapSvg();
    var cn = 0;
    var win = $(this);
    $('.ham-icon').click(function(){
        if(cn == 0){
            var st = "hidden";
            cn = 1;
            $('header').addClass('bluebg');
            $('.cross-btn2').show();
            $('html').css('overflow', 'hidden');
        }else{
            var st = "unset";
            cn = 0;
            $('header').removeClass('bluebg');
            $('.cross-btn2').hide();
            $('html').css('overflow-y', 'visible');
            $('html').css('overflow-x', 'hidden');
        }
        $("body").css("overflow", st);
        $(".my-sidenav1").slideToggle("slow");
        // $(this).text( $(this).text() == 'Menu' ? "Close" : "Menu");
    });
    $(".my-sidenav").accordion();

    $('.new-navopen').hover(function() {
     var id = $(this).attr('data');

     $(".newnavv").hide();
     $(".nv"+id).show();
 });

    $(".nv1").mouseleave(function(){
        $(this).hide();

    });

    $(".nv2").mouseleave(function(){

        $(this).hide();
    });

    $(".nv3").mouseleave(function(){

        $(this).hide();
    });
    $(".nv4").mouseleave(function(){

        $(this).hide();
    });


});

$('.ham-icon1').click(function(){
  $('.my-sidenav').css('max-width', '100%');
});

$('.cross-btn1').click(function(){
  $('.my-sidenav').css('max-width', '0px');
}); 


$('.panel-collapse').on('shown.bs.collapse', function (e) {
   var $panel = $(this).closest('.panel');
   $('html,body').animate({
     scrollTop: $panel.offset().top-80
 }, 500); 
});

$('.accordion-collapse').on('shown.bs.collapse', function (e) {
 var $panel = $(this).closest('.accordion-item');
 $('html,body').animate({
   scrollTop: $panel.offset().top-80
}, 1000); 

});


     /*
 * Replace all SVG images with inline SVG
 */
function collectMapSvg() {
    $('img.mapsvg').each(function(){
  //$('.'+cls).css({ fill: "#ff0000" });
      var $img = $(this);
      var imgID = $img.attr('id');
      var imgClass = $img.attr('class');
      var imgURL = $img.attr('src');

      $.get(imgURL, function(data) {
        // Get the SVG tag, ignore the rest
        var $svg = $(data).find('svg');

        // Add replaced image's ID to the new SVG
        if(typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if(typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass+' replaced-svg');
        }

        // Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');

        // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
        if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
        }

        // Replace image with new SVG
        $img.replaceWith($svg);

    }, 'xml');

  });
}



// $(window).on("scroll", function() {
//   if($(this).scrollTop() > 70) {
//    $('#header').addClass("sticky"); 
// } 
// else {
//    $('#header').removeClass("sticky");
// } 

// });

$(".banner-slider.owl-carousel").owlCarousel({
   autoplay: false,
   loop: false,
   dots:false,   
   nav: true,
   navText:"",
   touchDrag: true,
   mouseDrag: false,
   smartSpeed: 2000,
   animateIn: 'fadeIn',
   animateOut: 'fadeOut',
   responsive: {
     0: {
       items: 1
   },
   600: {
       items: 1
   },
   1000: {
    items:1
}
} 
});




$(".people-slider.owl-carousel").owlCarousel({
   autoplay: true,
   loop:true,
   dots:true,   
   nav: false,
   navText:"",
   touchDrag: true,
   mouseDrag: false,
   smartSpeed: 1000,
   autoplayTimeout:8000, 
   margin:15,
   responsive: {
     0: {
       items: 1
   },
   600: {
       items: 1
   },
   1000: {
    items:1
}
} 
});

$(document).ready(function () {
    var sync1 = $("#sync1");
    var sync2 = $("#sync2");
    var slidesPerPage = 22;
    var syncedSecondary = true;

    sync1.owlCarousel({
        items: 3,
        slideSpeed: 1000,
        nav: false,
        autoplay: false,
        dots: false,
        loop: false,
        margin:50,
        responsiveRefreshRate: 200,
        responsive: {
            0: { items: 1 },
            576: { items: 4 },
            768: { items: 5 },
            992: { items: 6 },
            1200: { items: 3 }
        }
    }).on('changed.owl.carousel', syncPosition);

    sync2.owlCarousel({
        items: slidesPerPage,
        dots: false,
        nav: false,
        smartSpeed: 200,
        slideSpeed: 500,
        margin: 0,
        responsiveRefreshRate: 100,
        responsive: {
            0: { items: 6 },
            576: { items: 4 },
            768: { items: 5 },
            992: { items: 6 },
            1200: { items: 22 }
        }
    }).on('changed.owl.carousel', syncPosition2);

    function syncPosition(el) {
        let current = el.item.index;
        sync2.find(".owl-item").removeClass("current").eq(current).addClass("current");
        let onscreen = sync2.find('.owl-item.active').length;
        let start = sync2.find('.owl-item.active').first().index();
        let end = sync2.find('.owl-item.active').last().index();

        if (current > end) {
            sync2.trigger('to.owl.carousel', [current, 100, true]);
        }
        if (current < start) {
            sync2.trigger('to.owl.carousel', [current - onscreen + 1, 100, true]);
        }
    }

    function syncPosition2(el) {
        if (syncedSecondary) {
            var number = el.item.index;
            sync1.trigger("to.owl.carousel", [number, 100, true]);
        }
    }

    sync2.on("click", ".owl-item", function (e) {
        e.preventDefault();
        var index = $(this).index();
        sync1.trigger("to.owl.carousel", [index, 300, true]);
    });

        // Set default active item
    sync2.find(".owl-item").eq(0).addClass("current");
});