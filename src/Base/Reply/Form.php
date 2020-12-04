<?php


namespace Base\Reply;


class Form
{
    protected $params = [];
    /**
     * @param string $name
     * @param string $value
     * @param bool $required
     * @param int $step
     * @param string $min
     * @param string $max
     * @return string
     */
    public function getInputNumber($name = "input_number", $value = "", $required = false, $step = 1, $min = "", $max = "" )
    {
        $required = $required ? "required='required'" : "";
        $min = $min !== "" ? "min=\"$min\"" : "";
        $max = !empty($max) ? "max=\"$max\"" : "";
        $uniqueID = $this->random(5);
        return "<div class=\"number-bock\">
                    <input type=\"number\" id=\"number-box-$uniqueID\" name=\"$name\" " . $required . " class=\"input-number input\"  ". $min . " ". $max . " step=\"$step\" value=\"$value\">
                    <button id=\"inc\" type=\"button\" tabindex=\"1\" onclick=\"this.parentNode.querySelector('#number-box-$uniqueID').stepUp()\"  class=\"spinner-button\"></button>
                    <button id=\"dec\" type=\"button\" tabindex=\"-1\" onclick=\"this.parentNode.querySelector('#number-box-$uniqueID').stepDown()\" class=\"spinner-button\"></button>
                </div>";
    }
    public function getHidden($value, $name)
    {
        return '<input type="hidden" value="' . $value . '" name="' . $name . '">';
    }
    /**
     * @param string $value
     * @param string $name
     * @return string
     */
    public function getTextBox($name = "textbox", $value = "")
    {
        return "<input type=\"text\" class=\"input\" name=\"$name\" value=\"$value\">";
    }

    /**
     * @param string $value
     * @param string $name
     * @return string
     */
    public function getPassword($name = "textbox", $value = "")
    {
        return "<input type=\"password\" class=\"input\" name=\"$name\" value=\"$value\">";
    }
    /**
     * @param string $name
     * @param array $value
     * @param array|string[] $nameClass
     * @return string
     */
    public function getSelector($name = "selector", array $value = [], array $nameClass = ['input'] )
    {
        $output = "<select name=\"$name\" class=\"" . implode(' ', $nameClass) ."\" >";
        foreach ($value['options'] as $optionKey => $option)
        {
            $selected = !empty($option['selected']) ? "selected" : "";
            $output .= "<option value=\"$optionKey\" $selected  >" . $option['label'] ."</option>";
        }
        $output .= "</select>";
        return $output;
    }

    /**
     * @param $valueRadio
     * @param string $name
     * @param array $values
     * @return string
     */
    public function getRadio($valueRadio, $name = 'radio', array $values = [] )
    {
        $output = "<ul class=\"input-radio\">";
        foreach ($values as $key => $value)
        {
            $valueInput = $value['value'];
            $getStyle = $valueRadio == $valueInput ? 'block' : 'none';
            $checked = $valueRadio == $valueInput ? 'checked' : '';
            $disabled = $valueRadio != $valueInput ? 'is-disabled' : '';
            $nameInput = $value['name'];
            $output .= "<li class=\"input-radio-choice input-hide\">";
            $output .= " <label class=\"radio\">";
            $output .= "<input type=\"radio\" name=\"$name\" value=\"$valueInput\" class='mater-input' $checked>";
            $output .= " <i aria-hidden=\"true\"></i>";
            $output .= "<span class=\"radio-label\">$nameInput</span>";
            $output .= "</label>";
            if(isset($value['input']) && !empty($value['input']))
            {
                $otherInput = $value['input'];
                $output .= "<ul class=\"inputChoices-dependencies $disabled\" style='display: $getStyle'>";
                $output .=  "<li class=\"inputChoices-choice\">";
                if(isset($otherInput['value']))
                {
                    if(isset($otherInput['accept']))
                    {
                        $this->params['accept'] = $otherInput['accept'];
                    }
                    if(isset($otherInput['nameFunction']))
                    {
                        $this->params['nameFunction'] = $otherInput['nameFunction'];
                    }
                    if(isset($otherInput['image']))
                    {
                        $this->params['image'] = $otherInput['image'];
                    }
                    $nameFunction = "get" . $otherInput['type'];
                    $output .= $this->{$nameFunction}($key, $otherInput['value']);
                }
                else
                {
                    $outputInput = [];
                    foreach ($otherInput as $inputKey => $input)
                    {

                        switch ($input['type'])
                        {
                            case 'number' :
                                $outputInput[] = $this->getInputNumber($inputKey, $input['value'], $input['required'], $input['step'], $input['min'], $input['max']) . (isset($input['otherText']) ? $input['otherText'] : '') ;
                                break;
                            default :
                                if(isset($input['accept']))
                                {
                                    $this->params['accept'] = $input['accept'];
                                }
                                if(isset($input['nameFunction']))
                                {
                                    $this->params['nameFunction'] = $input['nameFunction'];
                                }
                                if(isset($input['image']))
                                {
                                    $this->params['image'] = $input['image'];
                                }
                                $nameFunction = "get" . $input['type'];
                                $outputInput[] = $this->{$nameFunction}($inputKey, $input['value']);
                                break;
                        }
                    }
                    $output .= " <div class=\"inputGroup\" >";
                    $output .= implode('<span class="inputGroup-splitter"></span>', $outputInput);
                    $output .= "</div>";
                }
                $output .= "</li>";
                $output .= "</ul>";
            }
            $output .= "</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    /**
     * @param string $name
     * @param array $value
     * @return string
     */
    public function getTextarea($name = 'textarea', $value = []) {
        $required = $value['required'] ? 'required' : '';
        $row = $value['rows'] ?  $value['rows'] : 5;
        $minlength = $value['minlength'] ? " minlength=\"" .  $value['minlength'] . "\"" : '';
        return "<textarea name=\"" . $name . "\" " . $minlength . " class=\"input\" rows=\"" . $row . "\" " . $required . ">"  . $value['value'] .  "</textarea>";
    }
    public function getEditor($name = 'editor', $value = '')
    {
        return "<div class=\"editor\">
                    <div class=\"button-editor\">
                    </div>
                    <div class=\"fr-editor-write\" dir=\"ltr\" style=\"max-height: 930px; overflow: auto;\">
                        <div class=\"editor-write\" dir=\"ltr\" contenteditable=\"true\" style=\"min-height: 100px;\"
                             aria-disabled=\"false\" spellcheck=\"true\">
                             $value
                        </div>
                    </div>
                    <textarea id=\"editor\" name=\"$name\" minlength=\"10\" class=\"input\"
                              rows=\"5\">
                              $value
                            </textarea>
                </div>";
    }
    /**
     * @param string $name
     * @param array $value
     * @param string $nameFunction
     * @param bool $image
     * @return string
     */
    public function getUploader($name = "uploader", $value = [])
    {
        if(empty($value))
        {
            return "<input type=\"file\" name=\"$name\" $this->params class=\"input uploader\" >";
        }
        else
        {
            $accept = $this->params['accept'];
            $image = $this->params['image'];
            if (isset($this->params['nameFunction'])){
                $url = $value->{$this->params['nameFunction']}();
            }
            else {
                $url = $this->params['value'];
            }
            $image = $image ? "<span class=\"logo-attachment-figure\">
                              <img src=\"$url\">
                        </span>" : "";

            return "<div class=\"logo-attachment\">
                        $image
                        <span class=\"logo-attachment-input\">
                            <input type=\"file\" name=\"$name\" $accept class=\"input uploader\" value='$url'>
                        </span>
                        <input type='hidden' value='$url' name='oldValue'>
                      </div>";
        }

    }
    public function setParams($params){
        $this->params = $params;
    }
    /**
     * @param $car
     * @return string
     */
    public function random($car)
    {
        $int = "";
        $chaine = "123456789";
        srand((double)microtime() * 1000000);
        for($i = 0; $i < $car; $i++) {
            $int .= $chaine[rand()%strlen($chaine)];
        }
        return $int;
    }
    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    public function getDisplayOrder($name = "display_order", $value = 0)
    {
        return $this->getInputNumber($name, $value, false,1,0);
    }
}