<div class="p-main-header">
    <div class="p-title">
        <h1 class="p-title-value">
            <?= Base\BaseApp::phrase('confirm_action') ?>
        </h1>
    </div>
</div>

<div class="block-delete">
    <div class="block-text">
        <p><?= Base\BaseApp::phrase('please_confirm_that_you_want_to_delete_the_following:') ?></p>
        <strong><a href=""><?= $params->title ?></a> </strong>
    </div>
    <form action="<?= $params->link ?>" method="post">
        <div class="formSubmitRow-main">
            <div class="formSubmitRow-bar"></div>
            <div class="formSubmitRow-controls" >
                <button type="submit" name="delete" value="1" class="button--primary button button--icon button--icon--delete">
                <span class="button-text"><?= Base\BaseApp::phrase('delete') ?></span>
                </button>
            </div>
        </div>
    </form>
</div>