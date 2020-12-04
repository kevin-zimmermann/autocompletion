<div class="CanvasMenu">
    <div class="menu-nav-left"></div>
    <div class="menu-nav-right">
        <div class="CanvasMenu-content">
            <div class="CanvasMenu-header">
                Menu
                <span class="CanvasMenu-close"></span>
            </div>
            <div class="CanvasMenu-nav CanvasMenu-cat <?=  in_array($_SESSION['currentPage'], ['room', 'emoji', 'conversation']) ? 'active' : ''; ?>">
                <div class="CanvasMenu-titre">
                    <i class="far fa-sliders-h fa-fw"></i> Setup <span class="CanvasMenu-open"></span>
                </div>
                <div class="CanvasMenu-all-item" <?=  in_array($_SESSION['currentPage'], ['room', 'emoji', 'conversation']) ? 'style="display: block;"' : ''; ?>>
                    <div class="CanvasMenu-item <?= $_SESSION['currentPage'] == 'room' ? 'is-current-page' : ''; ?>">
                        <a href="<?= $app->buildLink('admin:room') ?>"> <?= \Base\BaseApp::phrase('room') ?></a>
                    </div>
                    <div class="CanvasMenu-item <?= $_SESSION['currentPage'] == 'emoji' ? 'is-current-page' : ''; ?>">
                        <a href="<?= $app->buildLink('admin:emoji') ?>"> <?= \Base\BaseApp::phrase('emoji') ?></a>
                    </div>
                    <div class="CanvasMenu-item <?= $_SESSION['currentPage'] == 'conversation' ? 'is-current-page' : ''; ?>">
                        <a href="<?= $app->buildLink('admin:conversation') ?>"> <?= \Base\BaseApp::phrase('conversations_list') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>