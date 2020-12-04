function getUrlHash(){
    if( window.location.hash ){
        return window.location.hash.replace('#', '');
    }
    return false;
}
console.log(getUrlHash());
$(document).ready(() => {
    if(getUrlHash())
    {
        let currentTabActive = $('.block-tab [data-tab-id=\'' + getUrlHash() + '\']');
        if(currentTabActive.length)
        {
            currentTabActive.addClass('hasActive');
            $('#' + getUrlHash()).addClass('tabHasActive');
        }
        else
        {
            let currentTabActive = $('.block-tab [data-current-active=\'true\']');
            currentTabActive.addClass('hasActive');
            let classCurrentActive = currentTabActive.data('tab-id');
            $('#' + classCurrentActive).addClass('tabHasActive');
        }

    }
    else
    {
        let currentTabActive = $('.block-tab [data-current-active=\'true\']');
        currentTabActive.addClass('hasActive');
        let classCurrentActive = currentTabActive.data('tab-id');
        $('#' + classCurrentActive).addClass('tabHasActive');
    }

    $('.block-tab ul li').click(function () {
        let clickClassId = $(this).data('tab-id');
        $.each($('.block-tab ul li'), function () {
            $(this).removeClass('hasActive');
        });
        $.each($('.block-tab-content .block-tab-style'), function () {
            $(this).removeClass('tabHasActive');
        });
        $('#' + clickClassId).addClass('tabHasActive');
        $(this).addClass('hasActive')
    });
});