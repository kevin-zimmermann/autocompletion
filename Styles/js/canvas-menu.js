function closeMenu($this){
    $parent = $this.closest('body');
    setTimeout(function(){
        $('.CanvasMenu').removeClass('active')
    },740);
    $parent.find('.menu-nav-left').animate({opacity:0});
    $parent.find('.menu-nav-right').animate({left: -$parent.find('.menu-nav-left').width()}, 700);
}
function existMenu($parent){
    $parent.find('.categories-header').animate({
        opacity: 0,
    }, 100).queue(function (next) {
        $(this).removeClass('is-category-active');
        next();
    });
}
$(document).ready(function() {
    $(window).resize(function() {
        if($(window).width() > 900)
        {
            $parent = $('.CanvasMenu');
            $parent.removeClass('active');
            $parent.find('.menu-nav-left').css('opacity', '');
            $parent.find('.menu-nav-right').css('left', '');
        }
    });
    $(window).scroll(function() {
        $header = $('header');
        if($(window).scrollTop() <= 20 && $header.hasClass('is-scroll'))
        {
            $header.removeClass('is-scroll');
        }
        else if (!$header.hasClass('is-scroll') && $(window).scrollTop() > 20)
        {
            $header.addClass('is-scroll');
        }
    });
});