

function renderHtml(errors)
{
    var output = "";
    if(errors.length === 1)
    {
        output = errors[0];
    }
    else
    {
        output += "<ul>";
        $.each(errors, function(Key, value) {
            output += "<li>" + value + "</li>";
        });
        output += "</ul>";
    }
    return output;
}
function getFormAjax(zIndex = 100000000)
{
    $('.form-submit-ajax').submit(function(event){
        event.preventDefault();
        let row = $(this);
        var form = $(this)[0];
        var data = new FormData(form);
        $.each($('.input'), function () {
            var input = $(this)[0];
            if(input.type !== 'file')
            {
                data.append(input.name, input.value);
            }
            else if(!$(this).data('type-count') === 'false')
            {
                data.append(input.name, input.files[0]);
            }
        });
        let originalEvent = $(event.originalEvent.submitter);
        if(originalEvent.hasClass('count_input_more'))
        {
            data.append(originalEvent.attr('name'), originalEvent.val());
        }
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            enctype: 'multipart/form-data',
            dataType: 'json',
            processData: false,
            contentType: false,
            cache: false,
            success:function(data){
                console.log(data["error"])
                if(data['error'].length === 0)
                {
                    console.log(data)
                    window.location.replace(data['link']);
                    if(row.find('#refresh-form').length)
                    {
                        window.location.reload();
                    }
                }
                else
                {
                    var renderError = renderHtml(data['error']);
                    $('.get-error').addClass('active-overlay')
                        .css('z-index', zIndex)
                        .animate({opacity:1},
                            {duration: 100})
                        .find('.blockMessage')
                        .html(renderError);
                }
            },
            error: function(errorThrown){console.log(errorThrown.responseText)}
        });
        return false;
    });
}
$(document).ready(function() {
    getFormAjax();
    $('.get-error .js-overlayClose').click(function () {
        leavePopup($('.get-error'));
    });
    $('.get-error').click(function(e) {
        $div =  $(this).find('.overlay');
        if(!$(e.target).is($div) && !$.contains($div[0],e.target))
        {
            leavePopup($(this));
        }
    });
    deleteImg();
    $('#changeFile').change(function () {
        let url = $(this).data('link-file');
        let fd = new FormData();
        let files = $(this)[0].files[0];

        if(files !== undefined)
        {
            fd.append('file', files);
            $.ajax({
                url : url,
                method : 'POST',
                dataType: 'json',
                data : fd,
                contentType: false,
                processData: false,
                success : (data) => {console.log(data)
                    let classImg = $('.list-product-img');
                    let div = $('<div></div>');
                    div.addClass('content-img');
                    div.html(renderHtmlContent(data));
                    classImg.append(div);
                    deleteImg();
                },
                error : function (el, statue) {
                    console.log(el.responseText)
                }
            })
        }

    });
});
function deleteImg()
{
    $('.delete-img').click(function (e) {
        e.preventDefault();
        $.ajax({
            url : $(this).data('delete-img'),
            method : 'post',
            dataType : 'json',
            data : {
                id : $(this).data('product-img-id')
            },
            success : (data) => {
                $('[data-product-img-id=' + data.imgId + ']').closest('.content-img').remove();
            }
        });
        return false;
    });
}
function renderHtmlContent(value) {
    let html = "<div class=\"content-image-inner\">";
    html += "<img src='" + value.urlLog + "' alt='" + value.name + "' >";
    html += "</div>";
    html += "<div class=\"content-image-inner\">";
    html += value.name
    html += "</div>";
    html += '<input type="hidden" name="product_img[]" value="' + value.id + '">';
    html += "<div class=\"img-action\">";
    html += "<button class=\"button delete-img\" data-delete-img=\"" + value.urlDelete + "\" data-product-img-id=\"" + value.id  + "\">Delete</button>";
    html += "</div>";
    return html;
}