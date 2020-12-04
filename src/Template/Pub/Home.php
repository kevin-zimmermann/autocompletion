<?php $visitor = Base\BaseApp::visitor(); ?>
<div class="home-main-container">
    <div class="chat-presentation">
        <div class="content-presentation">
            <a href="<?= $app->buildLink('Pub:') ?>">
                <img class="logo-nyancat" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/logoNyanChat.png"
                     alt="<?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?>">
            </a>
            <div class="text-explication">
                <h1>
                    Nyan Chat, chat va tchatter !
                </h1>
                <p>Confinement, télétravail, chat n'est pas un problème avec Nyan Chat ! Vous pouvez rester en contact
                    avec le monde entier !</p>
            </div>
            <a class="button button-chat-home"
               href="<?= $visitor->exists() ? $app->buildLink("Pub:chat") : $app->buildLink("Pub:login") ?>"> Testez ici
                !</a>
        </div>
    </div>
</div>
<div class="presentation-chat">
    <div class="blockex">
        <div class="block-text">
            <div class="title-presentation">
                <h2 class="title-to-explain">
                    Parlez avec vos amis, votre famille ou bien des inconnus à travers le monde !</h2>
            </div>
            <div class="explain-text">
                <p> Grâce à notre chat ultra-rapide, vous pouvez garder contact avec vos proches !</p>
            </div>
        </div>
        <div class="other-side-presentation right">
            <img src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/private.png"
                 alt="screenshot-private-message">
        </div>
    </div>

    <div class="blockex">
        <div class="other-side-presentation left">
            <img src="<?= \Base\BaseApp::getBaseLink() ?>Styles/image/public.png"
                 alt="screenshot-private-message">
        </div>
        <div class="block-text d">
            <div class="title-presentation">
                <h2 class="title-to-explain"> Possibilité de pouvoir suivre des conversations entre plusieurs
                    utilisateurs ! </h2>
            </div>
            <div class="explain-text">
                <p>Pour ça, il faut d'avoir un compte !</p>
            </div>
        </div>

    </div>

</div>