class Emoji {
    constructor(classMaster, classButton) {
        this.classMaster = classMaster;
        this.classButton = classButton;
        this.currentCursor = new Array();
        this.ie =  (typeof document.selection != "undefined" && document.selection.type !== "Control") && true;
        this.w3 = (typeof window.getSelection != "undefined") && true;
    }
    popup()
    {
        var self = this;
        $.each($('.' + this.classButton), function () {
            let random = self.random(1, 10000);
            $(this).attr('id', 'id-smiley-button-' + random);
            self.currentCursor[random] = 0;
        })
        $('.' + this.classButton).click(function () {
            var parent = $(this).closest('.' + self.classMaster);
            var selfClick = this;
            let offset = $(this).offset();
            let idClick = $(this).attr('id').substr(17)
            let classPopup = $('#id-popup-smiley-' + idClick + '.smiley-start');
            if(classPopup.length && classPopup.hasClass('smiley-open'))
            {
                self.leavePopup($('.smiley-start'))
            }
            else
            {
                self.append(self, parent, selfClick, offset)
            }
        })
        $(window).on('resize', function(){
            $.each($('.smiley-start'), function () {
                if($(this).hasClass('smiley-open'))
                {
                    let id = $(this).attr('id').substr(16);

                    let offset = $('#' + 'id-smiley-button-' + id + '.' + self.classButton).offset();
                    let percentage = ($(this).width() * (90 / 100));
                    let left = offset.left - percentage;
                    $(this).css('left', left).css('display', 'block').animate({opacity:1},
                        {duration: 100});
                }
            })

        });
        $('.' + this.classMaster + ' div[contenteditable=true]').keyup(function () {
            let parent = $(this).closest('.' + self.classMaster);
            let id = parent.find('.' + self.classButton).attr('id');
            id = id.substr(17)
            self.currentCursor[id] = self.getCaretPosition($(this).get(0), self);
        }).mouseup(function () {
            let parent = $(this).closest('.' + self.classMaster);
            let id = parent.find('.' + self.classButton).attr('id');
            id = id.substr(17)
            self.currentCursor[id] = self.getCaretPosition($(this).get(0), self);
        })

        this.clickBody();
    }
    append(self, parent, selfClick, offset)
    {
        $.ajax({
            url : parent.data('url-smiley'),
            method : 'post',
            dataType : 'json',
            success : (data) => {
                let idClick = $(selfClick).attr('id').substr(17)

                if($('#id-popup-smiley-' + idClick + '.smiley-start').length)
                {
                    let classPopup = $('#id-popup-smiley-' + idClick + '.smiley-start');
                    classPopup.addClass('smiley-open');
                    let html = '';
                    data.infos.forEach((value) => {
                        html += '<div class="smiley-content">';
                        html += '<div class="smiley-category">' + value.category.title + '</div>';
                        html += '<div class="smiley-lists">';
                        $.each(value.find, (key, emoji) => {
                            html += '<div class="smiley-list">';
                            html += '<img src="' + data.baseUrl + emoji.image_url + '" alt="' + emoji.title + '" data-emoji-id="' + emoji.emoji_id + '" class="click-smiley">'
                            html += '</div>';
                        })
                        html += '</div>';
                        html += '</div>';
                    })
                    $('.smiley-contents').html(html);
                    let percentage = (classPopup.width() * (90 / 100));
                    let left = offset.left - percentage;
                    classPopup.stop(true).css('top',  offset.top - (classPopup.height() + 20)).css('left', left).css('display', 'block').animate({opacity:1},
                        {duration: 100});
                    $('.smiley-arrow').css('left', classPopup.width() - 25 + 'px');
                }
                else
                {
                    let id =  $(selfClick).attr('id').substr(17);
                    let html = '<div class="smiley-start" id="id-popup-smiley-' + id + '">';
                    html += '<div class="smiley-arrow"></div>';
                    html += '<div class="smiley-contents">';
                    $(html).attr('id', 'id-popup-' + id);
                    data.infos.forEach((value) => {
                        html += '<div class="smiley-content">'
                        html += '<div class="smiley-category">' + value.category.title + '</div>'
                        html += '<div class="smiley-lists">'
                        $.each(value.find, (key, emoji) => {
                            html += '<div class="smiley-list">';
                            html += '<img src="' + data.baseUrl + emoji.image_url + '" alt="' + emoji.title + '" data-emoji-id="' + emoji.emoji_id + '" class="click-smiley">'
                            html += '</div>';
                        })
                        html += '</div>';
                        html += '</div>';
                    })
                    html += '</div>';
                    html += '</div>';
                    $('body').append(html);
                    let classPopup = $('#id-popup-smiley-' + idClick + '.smiley-start');
                    let percentage = (classPopup.width() * (90 / 100));
                    let left = offset.left - percentage;
                    classPopup.stop(true).css('top',  offset.top - (classPopup.height() + 20)).css('left', left).css('display', 'block').animate({opacity:1},
                        {duration: 100});
                    classPopup.addClass('smiley-open');
                    $('.smiley-arrow').css('left', classPopup.width() - 25 + 'px');
                }

                self.clickSmiley(self);
            },
        })
    }
    save(id)
    {
        let master = $('#' + id);
        let html = master.find('div[contenteditable=true]').html();
        master.find('textarea').val(html)
    }
    random(min, max)
    {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    leavePopup(getPopup)
    {
        getPopup.animate({opacity:0}, {duration: 100}).delay('100').queue(function (next) {
            $(this).removeClass('smiley-open');
            next();
        });
    }
    clickBody()
    {
        var self = this;
        $('body').click(function (e) {
            let div =  $(this).find('.smiley-open');
            if(!$(e.target).is(div) && ( div.length && !$.contains(div.get(0),e.target) ))
            {
                self.leavePopup(div);
            }
        })
    }
    clickSmiley(self)
    {

        $('.click-smiley').click(function () {
            let parent = $(this).closest('.smiley-start');
            let id = parent.attr('id').substr(16);
            let button = $('#id-smiley-button-' + id);
            let img = '<img src="'  + $(this).attr('src') + '" alt="' + $(this).attr('alt') + '" data-emoji-id="'  + $(this).data('emoji-id') + '" class="smiley-in-content">';
            let counter = self.currentCursor[id];
            self.setCurrentCursorPosition(counter, self, document.getElementById('id-smiley-button-' + id), button);
            document.execCommand('insertHtml', false, img)
        })
    }
    getCaretPosition(element, self) {
        var caretOffset = 0;
        if (self.w3) {
            var range = window.getSelection().getRangeAt(0);

            var preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(element);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            caretOffset = preCaretRange.toString().length;
        } else if (self.ie) {
            var textRange = document.selection.createRange();
            var preCaretTextRange = document.body.createTextRange();
            preCaretTextRange.moveToElementText(element);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);
            caretOffset = preCaretTextRange.text.length;
        }
        return caretOffset;
    }
    createRange(node, chars, self, range) {
        if (!range) {
            range = document.createRange()
            range.selectNode(node);
            range.setStart(node, 0);
        }

        if (chars.count === 0) {
            range.setEnd(node, chars.count);
        } else if (node && chars.count >0) {
            if (node.nodeType === Node.TEXT_NODE) {
                if (node.textContent.length < chars.count) {
                    chars.count -= node.textContent.length;
                } else {
                    range.setEnd(node, chars.count);
                    chars.count = 0;
                }
            } else {
                for (var lp = 0; lp < node.childNodes.length; lp++) {
                    range = self.createRange(node.childNodes[lp], chars, range);

                    if (chars.count === 0) {
                        break;
                    }
                }
            }
        }
        return range;
    };

    setCurrentCursorPosition(chars, self, selfClick, button) {
        if (chars >= 0) {
            var selection = window.getSelection();
            let range = self.createRange(selfClick.parentNode.children[0], { count: chars }, self);

            if (range) {
                range.collapse(false);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }
    };
}