function leavePopup(getPopup) {
    getPopup.animate({opacity:0}, {duration: 100}).delay('100').queue(function (next) {
        $(this).removeClass('active-overlay');
        next();
    });
}
function ajaxDelete()
{
    $('a').click(function(event){
        if($(this).data('overley'))
        {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                type: "post",
                dataType: 'json',
                success:function(data){
                    $('.get-delete').addClass('active-overlay')
                        .animate({opacity:1},
                            {duration: 100})
                        .find('.overlay-content')
                        .html(data.html);
                },
                error: function(errorThrown){
                    console.log(errorThrown.responseText)
                }
            });
            return false;
        }
    });
}
$(document).ready(function() {

    ajaxDelete();
    $('.get-delete .js-overlayClose').click(function () {
        leavePopup($('.get-delete'));
    });
    $('.get-delete').click(function(e) {
        $div =  $(this).find('.overlay');
        if(!$(e.target).is($div) && !$.contains($div[0],e.target))
        {
            leavePopup($(this));
        }
    });
});