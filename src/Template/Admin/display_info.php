<div class="p-main-header">
    <div class="p-title ">
        <h1 class="p-title-value">
            <?= $params->title ?>
        </h1>
    </div>
</div>
<div class="block-formRow">
    <?php foreach ($params->infoParam->contents as $content) { ?>
        <div class="block-formRow-inner">
            <?php foreach ($content as $block) {?>
                <?php if($block['type'] == 'value') { ?>
                    <dl class="formRow">
                        <dt>
                            <div class="block-form-label">
                                <?= $block['label'] ?>:
                            </div>
                        </dt>
                        <dd>
                            <?php if(!empty($block['link']) && $block['link']) { ?>
                                <a href="<?= $block['link'] ?>">
                                    <?= $block['value'] ?>
                                </a>
                            <?php } else {?>
                                <?= $block['value'] ?>
                            <?php } ?>
                        </dd>
                    </dl>
                <?php } elseif ($block['type'] == "array") {?>
                    <div class="block-body">
                        <?php foreach ($block['value'] as $value) { ?>
                            <dl class="formRow">
                                <dt>
                                    <div class="block-form-label">
                                        <?php if(!empty($value['label'])) { ?>
                                            <?= $value['label'] ?>:
                                        <?php } ?>
                                    </div>
                                </dt>
                                <dd>
                                    <?php if(!empty($value['link']) && $value['link']) { ?>
                                        <a href="<?= $value['link'] ?>">
                                            <?= $value['value'] ?>
                                        </a>
                                    <?php } else {?>
                                        <?= $value['value'] ?>
                                    <?php } ?>
                                </dd>
                            </dl>
                        <?php } ?>
                    </div>

                <?php } elseif ($block['type'] == "hr") {?>
                    <hr class="hr-form">
                <?php } elseif ($block['type'] == "h2") {?>
                    <div class="block-container">
                        <h2 class="block-header"><?= $block['h2'] ?></h2>
                        <div class="block-body block-body--contained block-row">
                            <?= $block['value'] ?>
                        </div>
                    </div>
                <?php } elseif ($block['type'] == "h3") {?>
                    <h3 class="block-minorHeader"><?= $block['h3'] ?></h3>
                    <div class="block-body block-body--contained block-row" >
                        <?= $block['value'] ?>
                    </div>
                <?php } elseif ($block['type'] == "h3simple") {?>
                    <h3 class="block-formSectionHeader">
                        <span class="block-formSectionHeader-aligner"><?= $block['value'] ?></span>
                    </h3>
                <?php } ?>

            <?php } ?>
        </div>
    <?php } ?>
</div>