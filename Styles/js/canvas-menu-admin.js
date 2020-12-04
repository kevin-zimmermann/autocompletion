function closeMenu($this){
    $parent = $this.closest('body');
    setTimeout(function(){
        $('.CanvasMenu').removeClass('active')
    },740);
    $parent.find('.menu-nav-left').animate({opacity:0});
    $parent.find('.menu-nav-right').animate({left: -$parent.find('.menu-nav-left').width()}, 700);
}
function existMenu($parent){
    $parent.find('.menu-deroulant').animate({
        opacity: 0,
    }, 100).queue(function (next) {
        $(this).removeClass('menu-active');
        next();
    });
}
$(document).ready(function() {
    $(window).resize(function() {
        if($(window).width() > 900)
        {
            $parent = $('.CanvasMenu');
            $parent.removeClass('active');
            $parent.find('.menu-nav-left').css('opacity', '')
            $parent.find('.menu-nav-right').css('left', '')
        }
    });
    $(window).scroll(function() {
        $header = $('header');
        if($(window).scrollTop() <= 5 && $header.hasClass('is-scroll'))
        {
            $header.removeClass('is-scroll');
        }
        else if (!$header.hasClass('is-scroll') && $(window).scrollTop() > 5)
        {
            $header.addClass('is-scroll');
        }
    });
    $('main').click(function() {
        $parent = $(this).closest('body');
        if($parent.find('.menu-deroulant').hasClass('menu-active'))
        {
            existMenu($parent);
        }
    });
    $('.hamburger-menu').click(function () {
        console.log('od');
        $parent = $(this).closest('body');
        $parent.find('.CanvasMenu').addClass('active');
        $parent.find('.menu-nav-left').animate({opacity:1});
        $parent.find('.menu-nav-right').animate({left: '0'}, 600);
    });
    $('.menu-nav-left').click(function () {
        closeMenu($(this));
    });
    $('.CanvasMenu-close').click(function () {
        closeMenu($(this));
    });
    $('.CanvasMenu-titre').click(function(){
        $parent = $(this).closest('.CanvasMenu-cat');
        if($parent.hasClass('active')){
            $parent.removeClass('active');
            $parent.find('.CanvasMenu-all-item').stop(true,true).slideUp(100);
        }else{
            $parent.addClass('active');
            $parent.find('.CanvasMenu-all-item').stop(true,true).slideDown(100);
        }
    });

});