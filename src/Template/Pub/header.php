<?php $visitor = Base\BaseApp::visitor(); ?>
<?php if($visitor->user_id && $visitor->is_admin) { ?>
    <div class="header-admin">
        <a href="<?= $app->buildLink('admin:') ?>">
            <?= \Base\BaseApp::phrase('admin') ?>
        </a>
    </div>
<?php } ?>
<div class="header-chat">
    <div class="header-logo">
        <a href="<?= $app->buildLink('Pub:') ?>">
            <img class="logo-nyancat" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/logoNyanChat.png" alt="<?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?>">
        </a>
    </div>
    <div class="header-button">
        <?php if (!$visitor->user_id) { ?>
            <ul>
                <li>
                    <a href="<?= $app->buildLink('Pub:login') ?>">
                        <i class="fas fa-sign-in"></i> <?= \Base\BaseApp::phrase('connexion') ?>
                    </a>
                </li>
                <li>
                    <a href="<?= $app->buildLink('Pub:login/register') ?>">
                        <i class="fas fa-key"></i> <?= \Base\BaseApp::phrase('registration') ?>
                    </a>
                </li>
            </ul>
        <?php } else { ?>
            <ul>
                <li>
                    <a href="<?= $app->buildLink('Pub:room') ?>">
                        <i class="far fa-comments"></i> <?= \Base\BaseApp::phrase('Chat') ?>
                    </a>
                </li>
                <li>
                    <a href="<?= $app->buildLink('Pub:account') ?>">
                        <i class="fas fa-user"></i> <?= $visitor->username ?>
                    </a>
                </li>
                <li>
                    <a href="<?= $app->buildLink('Pub:login/logout') ?>">
                        <i class="far fa-sign-out-alt"></i> <?= \Base\BaseApp::phrase('log_out') ?>
                    </a>
                </li>
            </ul>
        <?php } ?>
    </div>
</div>