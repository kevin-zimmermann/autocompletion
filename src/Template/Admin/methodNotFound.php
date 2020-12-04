<div class="p-main-header">
    <div class="p-title">
        <h1 class="p-title-value">
            <?= Base\BaseApp::phrase('error_title') ?>
        </h1>
    </div>
</div>
<div class="block">
    <div class="block-form block-error-method">
        <div class="block-miner">
            <?= Base\BaseApp::phrase('the_requested_page_could_not_be_found', [
                'code' => $code,
                'controller' => $controller,
                'action' => $action
            ]) ?>
        </div>
    </div>
</div>