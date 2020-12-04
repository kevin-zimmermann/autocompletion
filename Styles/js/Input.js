function MultipleSelect(type, nameSelect, moreParam = null)
{
    $('[type=' + type + ']').on('change', function(e) {
        $parent = $(this).closest('.' + nameSelect);
        $findInputChoices = $parent.find('.inputChoices-dependencies');
        if($parent.hasClass('input-hide'))
        {
            if($findInputChoices.hasClass('is-disabled'))
            {
                $findInputChoices.stop(true,true).slideDown(100);
                $findInputChoices.removeClass('is-disabled');
                $findInputChoices.find('.input').prop('disabled', false);
                $findInputChoices.find('[type=button]').prop('disabled', false)
            }
            else
            {
                $findInputChoices.stop(true,true).slideUp(100);
                $findInputChoices.addClass('is-disabled');
                $findInputChoices.find('.input').prop('disabled', true)
                $findInputChoices.find('[type=button]').prop('disabled', true)
            }
        }
        else
        {
            if($findInputChoices.find('[type=' + type +']:checked'))
            {
                $findInputChoices.find('.input').prop('disabled', false);
                $findInputChoices.find('[type=button]').prop('disabled', false)
            }
            else
            {
                $findInputChoices.find('.input').prop('disabled', true)
                $findInputChoices.find('[type=button]').prop('disabled', true)
            }
        }
        if(moreParam !== null)
        {
            moreParam(e);
        }

    })
}
var RadioDisable = function (e) {
    $.each($('.input-radio-choice'), function() {
        $parent = $(this).closest('.input-radio');
        if($(this).find('.mater-input').is(':checked') === false)
        {
            $findInputChoices = $(this).find('.inputChoices-dependencies');
            if($(this).hasClass('input-hide'))
            {

                $findInputChoices.stop(true,true).slideUp(100);
                $findInputChoices.addClass('is-disabled');
                $findInputChoices.find('.input').prop('disabled', true)
                $findInputChoices.find('[type=button]').prop('disabled', true)
            }
            else if(!$(e.target).is($parent) && !$.contains($parent[0],e.target))
            {
                $findInputChoices.find('.input').prop('disabled', true)
                $findInputChoices.find('[type=radio]').prop('disabled', true)
            }
        }
    })
}

$(document).ready(function() {
    MultipleSelect('checkbox', 'inputChoices-choice');
    MultipleSelect('radio', 'input-radio-choice', RadioDisable);
});