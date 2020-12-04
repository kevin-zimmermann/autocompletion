<div class="p-header-buttons p-header-buttons--main">
    <a class="hamburger-menu p-header-button p-header-button--nav" role="button" tabindex="0" aria-label="Menu">
        <i class="far fa-bars" aria-hidden="true"></i>
    </a>
    <a href="<?= $app->buildLink('admin:') ?>" class="p-header-button" aria-label="Home"><i class="far fa-home" aria-hidden="true"></i></a>
    <a href="<?= $app->buildLink('Pub:') ?>" class="p-header-button p-header-button--title" target="_blank"><?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?></a>
</div>
