$(document).ready(function () {
    $('[data-toggle=tooltip]')
        .mouseover(function () {
            let offset = $(this).offset();
            if($('.tooltip-start').length)
            {
                $('.tooltip-start .tooltip-content').html($(this).data('tooltip-title'));
                let percentage = ($('.tooltip-start').width() * (50 / 100)) - 5;
                let left = offset.left - percentage;
                $('.tooltip-start').css('display', 'block');
                $('.tooltip-start').stop(true).css('top',  offset.top - ($('.tooltip-start').height() + 5)).css('left', left).animate({opacity:1},
                    {duration: 100});
            }
            else
            {
                let html = '<div class="tooltip-start">';
                html += '<div class="tooltip-arrow"></div>';
                html += '<div class="tooltip-content">';
                html += $(this).data('tooltip-title');
                html += '</div>';
                html += '</div>';
                $('body').append(html);
                $('.tooltip-start').css('opacity', 0);
                let percentage = $('.tooltip-start').width()  * (50 / 100)- 5;
                let left = offset.left - percentage;
                $('.tooltip-start').stop(true).css('top',  offset.top - ($('.tooltip-start').height() + 5)).css('left', left).css('display', 'block').animate({opacity:1},
                    {duration: 100});
            }
        })
        .mouseout(function() {
            $('.tooltip-start').stop(true).animate({opacity:0}, {duration: 50}).queue(function (next) {
                $(this).css('display', 'none');
                next();
            });
        });
})