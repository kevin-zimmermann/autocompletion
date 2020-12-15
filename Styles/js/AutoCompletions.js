class AutoCompletions {
    constructor(id, options = {}) {
        this.id = id;
        this.options = options;
    }
    getAuto()
    {
        var self = this,
            option = this.options;

        $('#' + this.id).on('input', function () {
            let value = $(this).val();
            var input = this;
            if(value.length !== '')
            {
                $.ajax({
                    url : $(this).data('auto-url'),
                    method : 'post',
                    data : {
                        search : $(this).data('search'),
                        value : value,
                        entity : $(this).data('entity')
                    },
                    dataType : 'json',
                    success : function (data) {
                        $('#' + self.id).autocomplete({
                            delay: 100,
                            minLength: 2,
                            matchContains: true,
                            source: function(request, response) {
                                response($.map(data.entities, function(dataItem)  {
                                    let returnHtml = '';
                                    if(dataItem.logo !== undefined)
                                    {
                                        returnHtml += dataItem.logo;
                                    }
                                    return {
                                        returnHtml : returnHtml,
                                        value : dataItem.title,
                                        link : (dataItem.link ? dataItem.link : ''),
                                    }
                                }) );
                            },
                            select: function (event, ui){
                                if (ui.item.link){
                                    window.location = ui.item.link;
                                }
                            }
                        }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                            console.log(ul)
                            return $('<li>').data( "ui-autocomplete-item", item ).append('<div class="ui-content-auto">' + item.returnHtml + item.label + '</div>').appendTo(ul);
                        };
                    },
                    error: function(errorThrown){console.log(errorThrown.responseText)}
                })
            }
        })
    }
    run()
    {
        this.getAuto();
    }
    // appendHtml(values)
    // {
    //     let html = '<div class="contents-auto-completion">'
    //     html += this.html(values);
    //     return html + '</div>'
    // }
    // html(values)
    // {
    //     let html = '';
    //     $.each(values, (key, value) => {
    //         html += '<div class="content-auto-completion">'
    //         console.log(value)
    //         if(value.logo !== undefined)
    //         {
    //             html += value.logo
    //         }
    //         html += '<span>' + value.title + '</span>'
    //         html += '</div>'
    //     })
    //     return html;
    // }
}