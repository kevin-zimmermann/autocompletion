<div class="p-main-header">
    <div class="p-title">
        <h1 class="p-title-value">
            <?= Base\BaseApp::phrase('error_title') ?>
        </h1>
    </div>
</div>
<div class="block-with-border block-error">
    <div class="block-form block-error-method">
        <div class="block-miner">
            <?php if(\Base\BaseApp::getConfigOptions()->debug) {?>
                <?= Base\BaseApp::phrase('the_requested_page_could_not_be_found_by_x', [
                        'code' => $code,
                        'controller' => $controller,
                        'action' => $action
                ]) ?>
            <?php } else {?>
                <?= Base\BaseApp::phrase('the_requested_page_could_not_be_found') ?>
            <?php }?>
        </div>
    </div>
</div>