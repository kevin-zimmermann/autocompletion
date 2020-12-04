function getValueFormRow(value) {
    let html = "<dl class=\"formRow\">";
    html += '<dt>\n' +
        '     <div class="block-form-label">\n';
    if (value.label.length)
    {
        html += value.label + ':';
    }
    html +=   '</div>\n' +
        '</dt>';
    html += "<dd>";
    if(value.link)
    {
        html += '<a href="' + value.link + '" >';
        html += value.value;
        html += '</a>';
    }
    else
    {
        html += value.value;
    }
    html += "</dd>";
    html += "</dl>";
    return html;
}
function getValueH2(value) {
    let html = "<div class=\"block-container\">";
    html += "<h2 class=\"block-header\">" + value.h2 + '</h2>';
    html += '<div class="block-body block-body--contained block-row">';
    html += value.value;
    html += "</div></div>"
    return html;
}
function getValueH3(value) {
    let html = "<h3 class=\"block-minorHeader\">"+ value.h3 + '</h3>';
    html += '<div class="block-body block-body--contained block-row" >';
    html += value.value;
    html += "</div>"
    return html;
}
function getValueH3simple(value) {
    let html = " <h3 class=\"block-formSectionHeader\">";
    html += "<span class=\"block-formSectionHeader-aligner\">" + value.value + "</span>";
    html += "</h3>"
    return html;
}
function getValueArray(value) {
    let html = '<div class="block-body">';
    $.each(value.value, function (key, valueArray) {
        html += getValueFormRow(valueArray);
    })
    html += '</div>'
    return html;
}
function renderHtmlInfo(data) {
    let html = '';
    $.each(data.contents, function (key, value) {
        html += '<div class="block-formRow-inner">'
        $.each(value, function (keyOther, valueOther) {
            switch (valueOther.type) {
                case 'value' :
                    html += getValueFormRow(valueOther);
                    break;
                case 'hr' :
                    html += "<hr class=\"hr-form\">";
                    break;
                case 'h2' :
                    html += getValueH2(valueOther);
                    break;
                case 'h3' :
                    html += getValueH3(valueOther);
                    break;
                case 'h3simple' :
                    html += getValueH3simple(valueOther);
                    break;
                case 'array' :
                    html += getValueArray(valueOther);
                    break;

            }
        })
        html += '</div>';
    })
    return html;
}
function ajaxInfo()
{
    $('a').click(function(event){
        if($(this).data('overlay-info'))
        {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                type: "post",
                dataType: 'json',
                success:function(data){
                    let renderInfo = renderHtmlInfo(data);
                    let parent = $('.get-info');
                    parent.find('.popup-title')
                        .html(data['namePage']);
                    parent.find('.block-formRow')
                        .html(renderInfo);
                    parent.addClass('active-overlay')
                        .animate({opacity:1},
                            {duration: 100});
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

    ajaxInfo();
    $('.get-info .js-overlayClose').click(function () {
        leavePopup($('.get-info'));
    });
    $('.get-info').click(function(e) {
        $div =  $(this).find('.overlay');
        if(!$(e.target).is($div) && !$.contains($div[0],e.target))
        {
            leavePopup($(this));
        }
    });
});