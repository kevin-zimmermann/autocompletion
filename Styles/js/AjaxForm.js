var editorVar = false;

/**
 * @param key
 * @param value
 * @returns {string}
 */
function getTextBox(key, value)
{
    var required = value.required ? 'required' : '';
    return "<input type=\"text\" class=\"input\" name=\"" + key + "\" value=\"" + value.value + "\" " + required +">";
}

/**
 * @param key
 * @param value
 * @returns {string}
 */
function getPassword(key, value)
{
    var required = value.required ? 'required' : '';
    return "<input type=\"password\" class=\"input\" name=\"" + key + "\" value=\"" + value.value + "\" " + required +">";
}
/**
 * @param key
 * @param value
 * @returns {string}
 */
function getTextarea(key, value) {
    var required = value.required ? 'required' : '';
    $row = value.rows ?  value.rows : 5;
    $minlength = value.minlength ? " minlength=\"" + value.minlength + "\"" : '';
    return "<textarea name=\"" + key + "\" " + $minlength + " class=\"input\" rows=\"" + $row + "\" " + required + ">"  + value.value +  "</textarea>"
}
/**
 * @param key
 * @param value
 * @returns {string}
 */
function getInputNumber(key, value) {
    $required = value.required ? "required='required'" : "";
    $min = value.min !== "" ? "min=\"" + value.min + "\"" : "";
    $max = value.max ? "max=\"" + value.max + "\"" : "";
    $uniqueID = Math.floor(Math.random() * 100000);
    return "<div class=\"number-bock\">" +
        "<input type=\"number\" id=\"number-box-" + $uniqueID + "\" name=\"" + key + "\" " + $required + " class=\"input-number input\"  " + $min + " " + $max + " step=\"" + value.step + "\" value=\"" + value.value + "\">" +
        "<button id=\"inc\" type=\"button\" tabindex=\"1\" onclick=\"this.parentNode.querySelector('#number-box-" + $uniqueID + "').stepUp()\"  class=\"spinner-button\"></button>" +
        "<button id=\"dec\" type=\"button\" tabindex=\"-1\" onclick=\"this.parentNode.querySelector('#number-box-" + $uniqueID + "').stepDown()\" class=\"spinner-button\"></button>" +
        "</div>";
}

/**
 * @param key
 * @param value
 * @returns {string}
 */
function getSelector(key, value)
{
    $output = "<select name=\"" + key + "\" class=\"input\">";
    $.each(value.options, function(optionKey, option) {
        $selected = option.selected ? "selected" : "";
        $output += "<option value=\"" + optionKey + "\" " + $selected + ">" + option.label + "</option>";
    });
    $output += "</select>";
    return $output;
}

/**
 * @param key
 * @param value
 * @returns {string}
 */
function getSelectorByArray(key, value)
{
    $output = "<select name=\"" + key + "\" class=\"input\">";
    value.options.forEach(function (option, key) {
        $selected = option.selected ? "selected" : "";
        $output += "<option value=\"" + key + "\" " + $selected + ">" + option.label + "</option>";
    })
    $output += "</select>";
    return $output;
}
/**
 *
 * @param valueRadio
 * @param name
 * @param values
 * @returns {string}
 */
function getRadio(valueRadio, name = 'radio', values = [] )
{
    $output = "<ul class=\"input-radio\">";
    $.each(values.InputValue, function(optionKey, option) {
        console.log(name)
        $valueInput = option.value ;
        $getStyle = valueRadio === $valueInput ? 'block' : 'none';
        $checked = valueRadio === $valueInput ? 'checked' : '';
        $disabled = valueRadio !== $valueInput ? 'is-disabled' : '';
        $nameInput = option.name;
        $output += "<li class=\"input-radio-choice input-hide\">";
        $output += " <label class=\"radio\">";
        $output += "<input type=\"radio\" name=\"" + name + "\" value=\"" + option.value + "\" class='mater-input'" + $checked + ">";
        $output += " <i aria-hidden=\"true\"></i>";
        $output += "<span class=\"radio-label\">" + $nameInput + "</span>";
        $output += "</label>";
        if(option.input.length !== 0)
        {
            var otherInput = option.input;
            $output += "<ul class=\"inputChoices-dependencies " + $disabled + "\" style='display: " + $getStyle + "'>";
            $output +=  "<li class=\"inputChoices-choice\">";
            $nameFunction = "get" + otherInput.type;
            if( otherInput.type === 'TextBox')
            {
                $output += getTextBox(optionKey, otherInput);
            }
            if( otherInput.type === 'Uploader')
            {
                $output += getUploader(optionKey, otherInput);
            }
            $output += "</li>";
            $output += "</ul>";
        }
        $output += "</li>";
    });

    $output += "</ul>";
    return $output;
}

/**
 *
 * @param name
 * @param value
 * @returns {string}
 */
function getUploader(name,  value)
{
    return "<input type=\"file\" name=\"" + name + "\" " + value.value + " class=\"input\" >";
}
/**
 *
 * @param name
 * @param value
 * @returns {string}
 */
function getEditor(name,  value)
{
    return "<div class=\"editor\">\n" +
        "     <div class=\"button-editor\">\n" +
        "     </div>\n" +
        "     <div class=\"fr-editor-write\" dir=\"ltr\" style=\"max-height: 930px; overflow: auto;\">\n" +
        "          <div class=\"editor-write\" dir=\"ltr\" contenteditable=\"true\" style=\"min-height: 100px;\"\n" +
        "               aria-disabled=\"false\" spellcheck=\"true\">\n" +
                            value +
        "           </div>\n" +
        "      </div>\n" +
        "      <textarea id=\"editor\" name=\"" + name +"\" minlength=\"10\" class=\"input\"\n" +
        "                 rows=\"5\">\n" +
                        value +
        "       </textarea>\n" +
        "   </div>";
}
/**
 * @param data
 * @returns {string}
 */
function renderHtmlForm(data) {
    $ajax = data['ajax'] ? "form-submit-ajax" : "";
    $output = "<div class=\"block-formRow block-form\">\n" +
        "            <div class=\"block-miner\">" +
        "               <form action=\""+ data['saveURL'] +"\" method=\"post\" class='" + $ajax + " form-submit-action'>";
    $output += getHtmlInput(data['Input']);

    $output += "<div class=\"form-submit\">\n" +
        "          <div class=\"form-submit-controls\">\n" +
        "             <button type=\"submit\" name=\"submit\" value=\"1\" class=\"button\"><i class=\""+ data['logoButton'] +" fa-space-1x\"></i> "+ data['nameButton'] +"</button>\n" +
        "          </div>\n" +
        "        </div>";

    $output += "</form>" +
        "   </div>\n" +
        " </div>";

    return $output;
}
function getHidden(key, value) {
    return '<input type="hidden" name="' + key + '" value="' + value['value'] + '">'
}
/**
 *
 * @param values
 * @returns {string}
 */
function getHtmlInput(values)
{
    $output = "";
    $.each(values, function(Key, value) {
        if(value.type !== 'hidden')
        {
            $output += renderHtmlInput(Key, value);
        }
        else
        {
            $output += getHidden(Key, value);
        }
    });
    return $output;
}

/**
 * @param key
 * @param value
 * @returns {string}
 */
function renderHtmlInput(key, value)
{
    $output = "<dl class=\"formRow\">\n" +
        "                <dt>\n" +
        "                    <div class=\"block-form-label\">" +
                    "<label class=\"form-label\" >"+ value.title + ":</label>";
    $output += '</div></dt><dd>';

    switch (value.type) {
        case 'spinbox' :
            $output += getInputNumber(key, value);
            break;
        case 'textbox' :
            $output += getTextBox(key, value);
            break;
        case 'password' :
            $output += getPassword(key, value);
            break;
        case 'textarea' :
            $output += getTextarea(key, value);
            break;
        case 'selector' :
            $output += getSelector(key, value);
            break;
        case 'radio' :
            $output += getRadio(value.value, key, value);
            break;
        case 'editor' :
            $output += getEditor(key, value.value);
            editorVar = true;
            break;
        case 'uploader' :
            $output += getUploader(key, value.value);
            break;
    }
    $output += "<div class=\"" +
                    "form-explain\">" +
                    value.description +
                "</div></dd>";
    $output += "</dl>";
    return $output;
}
function ajaxForm()
{
    $('a').click(false, function(){
        if($(this).data('overley-form'))
        {
            $.ajax({
                url: $(this).attr('href'),
                type: "post",
                dataType: 'json',
                success:function(data){
                    var renderForm = renderHtmlForm(data);
                    var parent = $('.get-form');
                    parent.find('.popup-title')
                        .html(data['namePage']);
                    parent.find('.overlay-content')
                        .html(renderForm);
                    parent.addClass('active-overlay')
                        .animate({opacity:1},
                            {duration: 100});
                    if(editorVar)
                    {
                        var editorCreator = new editor();
                        editorCreator.run();
                    }

                    getFormAjax(1000000000);

                },
                error: function(errorThrown){console.log(errorThrown.responseText)}
            });
            return false;
        }
    });
}
$(document).ready(function() {
    ajaxForm();
    $('.get-form .js-overlayClose').click(function () {
        leavePopup($('.get-form'));
    });
    $('.get-form').click(function(e) {
        $div =  $(this).find('.overlay');
        if(!$(e.target).is($div) && !$.contains($div[0],e.target))
        {
            leavePopup($(this));
        }
    });
});