<div class="p-main-header">
    <div class="p-title">
        <h1 class="p-title-value">
            <?= !empty($params->title) ?  $params->title : null ?>
        </h1>
    </div>
</div>
<div class="block">
    <div class="block-form block-error-method">
        <div class="block-miner">
           <?= !empty($params->message) ?  $params->message : null ?>
        </div>
    </div>
</div>