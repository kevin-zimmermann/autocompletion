
<div class="p-main-header">
    <div class="p-title ">
        <h1 class="p-title-value">
            <?= $params->title ?>
        </h1>
    </div>
</div>

<?php
$value = $params->formParam;
$formInput = $params->formRepo;
$ajax = isset($value->ajax) && $value->ajax ? 'form-submit-ajax' : '';
?>
<div class="block-formRow block-form">
    <div class="block-miner">
        <form action="<?= $value->saveURL ?>" method="post"  enctype="multipart/form-data" class="<?= $ajax ?> form-submit-action">
            <?php foreach ($value->Input as $name => $valueInput){ ?>
                <dl class="formRow">
                    <dt>
                        <div class="block-form-label">
                            <label class="form-label" > <?= $valueInput['title'] ?> :</label>
                        </div>
                    </dt>
                    <dd>
                        <?php
                        if($valueInput['type'] === 'spinbox')
                        {
                            echo $formInput->getInputNumber($name, $valueInput['value'], $valueInput['required'], $valueInput['step'], $valueInput['min'], $valueInput['max']);
                        }
                        if($valueInput['type'] === 'textbox')
                        {
                            echo $formInput->getTextBox($name, $valueInput['value']);
                        }
                        if($valueInput['type'] === 'codemirror')
                        {
                            echo $formInput->getCodeMirror($name, $valueInput['value']);
                        }
                        if($valueInput['type'] === 'selector')
                        {
                            echo $formInput->getSelector($name, $valueInput);
                        }
                        if($valueInput['type'] === 'uploader')
                        {
                            $formInput->setParams([
                                'accept' => $valueInput['accept'],
                                'nameFunction' => $valueInput['nameFunction'],
                                'image' => $valueInput['image'],
                                'value' => $valueInput['valueUrl'],
                            ]);
                            echo $formInput->getUploader($name, $value);
                        }
                        if($valueInput['type'] === 'radio')
                        {
                            echo $formInput->getRadio($valueInput['value'], $name, $valueInput['InputValue']);
                        }
                        ?>
                        <div class="form-explain">
                            <?= $valueInput['description'] ?>
                        </div>
                    </dd>
                </dl>
            <?php } ?>
            <div class="p-submit">
                <button type="submit" name="submit" value="1" class="button"><i class="<?= $value->logoButton ?> fa-space-1x"></i> <?= $value->nameButton ?> </button>
             </div>
        </form>
    </div>
</div>
