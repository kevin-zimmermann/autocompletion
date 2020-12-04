class Refresh {
    constructor(className, classContent, classMaster, time = 1000) {
        this.className = className;
        this.time = time;
        this.classContent = classContent;
        this.classMaster = classMaster;
        if($('.' + classMaster).length && $('.' + className).length)
        {
            $('.' + classMaster).scrollTop($('.' + className)[0].scrollHeight)
        }

    }
    run()
    {
        var self = this;

        this.interval = setInterval(function(){self.refresh(self)}, this.time);

        this.apply();
    }
    refresh(self)
    {
        let classContent = $('.' + self.classContent);
        let last = classContent.last();
        let allId = [];
        let lastId = 0;
        if(last.length)
        {
            lastId = last.data('entity-id');
            $.each(classContent, function (key) {
                allId[key] = $(this).data('entity-id')
            })
        }
        $.ajax({
            url : $('.' + self.className).data('refresh'),
            method : 'post',
            data : {
                lastId : lastId,
                allId : allId.join(',')
            },
            dataType : 'json',
            success : (data) => {
                self.before(data);
                if(data.total)
                {
                    $.each(data.entities, function (key, value) {
                        let masterClass = $('.' + self.classMaster);
                        let className = $('.' + self.className);
                        console.log(className)
                        let totalScroll = masterClass.get(0).scrollHeight -  masterClass.height();
                        let isScroll = false;
                        if(totalScroll  <= masterClass.scrollTop())
                        {
                            isScroll = true;
                        }
                        className.append(self.appendHtml({
                            entity : value,
                            user : {
                                user_id : data.user_id
                            }
                        }));
                        if(isScroll)
                        {
                            masterClass.scrollTop(className[0].scrollHeight)
                        }
                    });
                }
                self.after(data)
            },
        });
    }
    before(data) {}
    after(data) {}
    appendHtml(data) {}
    apply() {}
}
class RefreshConversation extends Refresh
{
    appendHtml(data)
    {
        let html = '<div class="content-message ' + (data.entity.user_id === data.user.user_id ? 'conversation-user' : 'no-user') + '" data-entity-id="' + data.entity.message_id + '">'
        html += '<div class="content-message-inner">\n' +
            '                    <div class="message">';
        html += data.entity.message;
        html += '</div>\n' +
            '<div class="action-message">';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        return html;
    }
    submitMessage(content) {
        var self = this;
        console.log(content.html().trim());
        if(content.html().trim().length)
        {
            clearInterval(this.interval);
            $.ajax({
                url :  content.data('url-save'),
                method: 'post',
                data : {
                    message : content.html()
                },
                dataType : 'json',
                success : (data) => {
                    let container = $('.' + self.className);
                    container.append(self.appendHtml(data));
                    container.closest('.content-conversation-list').scrollTop(container[0].scrollHeight);
                    $(content).html('');
                    if($('.user-write').length)
                    {
                        $('.user-write').remove()
                    }
                    self.interval = setInterval(function(){self.refresh(self)}, self.time)
                    this.after(data)
                },
            })
        }
    }
    apply() {
        var action = false;
        var self = this;
        var editorClass = $('.post-message-content');
        editorClass.keydown(function () {
            let html = $(this).html().trim();
            if(html.length)
            {
                action = true;
                $.ajax({
                    url :  $(this).closest('.post-message').data('update'),
                    method: 'post',
                    data : {
                        value : action
                    },
                    dataType : 'json',
                    success : (data) => { },
                })
            }
            else if(action)
            {
                action = false;
                $.ajax({
                    url :  $(this).closest('.post-message').data('update'),
                    method: 'post',
                    data : {
                        value : action
                    },
                    dataType : 'json',
                    success : (data) => {
                        console.log(data)
                    },
                })
            }
        });
        editorClass.on('paste', function (e) {
            console.log( e.originalEvent)
            e.preventDefault();
            var text = e.originalEvent.clipboardData.getData("text/plain");
            document.execCommand("insertHTML", false, text);
        })
    }
    after(data) {
        if(data.write)
        {
            if(!$('.user-write').length)
            {
                let html = '<div class="user-write">'
                html += '<div class="content-message-inner">\n' +
                    '                    <div class="message">';
                html += '<div class="user-write-content"></div>'
                html += '</div>\n' +
                    '<div class="action-message">';
                html += '</div>';
                html += '</div>';
                html += '</div>';

                let className = $('.' + this.className);
                className.append(html);
            }
        }
        else
        {
            if($('.user-write').length)
            {
                $('.user-write').remove()
            }
        }
    }
}
class RefreshRoom extends Refresh
{
    appendHtml(data)
    {
        console.log(data.entity)
        let html = '<div class="print-message-content" data-entity-id="' + data.entity.message_id + '">' ;
        html += '<div class="print-message-inner">';
        html += '<div class="print-message-content-inner">';
        html += '<div class="logo-user">';
        html += data.entity.avatar
        html += '</div>';
        html += '<div class="message-content">';
        html += '<div class="print-message-username">';
        html += data.entity.username ;
        html += ' <span class="print-time-message">' + data.entity.post_date + '</span>';
        html += '</div>';
        html += '<div class="room-action-message-other">';
        html += '<div class="room-message">';
        html += data.entity.message;
        html += '</div>';
        console.log(data.entity.deleteUrl)
        if(data.entity.deleteUrl.length)
        {
            html += '<div class="room-action-message">\n' +
                '      <div class="room-action-message-inner">';
            html += '<a href="' + data.entity.deleteUrl + '" data-overley="true">'
            html += '<i class="fas fa-trash-alt"></i>';
            html += '</a>';
            html += '</div>';
            html += '</div>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        return html;
    }
    before(data) {
        if(data.allIds[0] !== "")
        {
            $.each(data.allIds, function (key, value) {
                if($('[data-entity-id=' + value + ']').length)
                {
                    $('[data-entity-id=' + value + ']').remove();
                }

            })
        }

    }

    after(data) {
        ajaxDelete()
    }

    submitMessage(content) {
        var self = this;
        if(content.html().trim().length)
        {
            clearInterval(this.interval);
            $.ajax({
                url :  content.data('url-save'),
                method: 'post',
                data : {
                    message : content.html()
                },
                dataType : 'json',
                success : (data) => {
                    let container = $('.' + self.className);
                    container.append(self.appendHtml(data));
                    container.closest('.print-message').scrollTop(container[0].scrollHeight);
                    $(content).html('');
                    self.after(data);
                    self.interval = setInterval(function(){self.refresh(self)}, self.time)
                },
                error: function(errorThrown){
                    console.log(errorThrown.responseText)
                }
            })
        }
    }
    apply() {
        var editorClass = $('.post-message-content');
        editorClass.on('paste', function (e) {
            e.preventDefault();
            var text = e.originalEvent.clipboardData.getData("text/plain");
            document.execCommand("insertHTML", false, text);
        });
    }

}