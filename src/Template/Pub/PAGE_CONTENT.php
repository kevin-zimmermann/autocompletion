<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <Link rel="stylesheet" href="http://jqueryui.com/resources/demos/style.css">
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxDelete.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxForm.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxError.js"></script>
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>/Styles/js/canvas-menu.js"></script>
    <link rel="stylesheet" href="<?= \Base\BaseApp::getBaseLink() ?>Styles/css/fa.css">
    <link rel="stylesheet" href="<?= \Base\BaseApp::getBaseLink() ?>Styles/css/pub.css" >
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/js/tab.js"></script>
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/js/tooltip.js"></script>
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/js/AutoCompletions.js"></script>
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/js/Refresh.js"></script>
    <script type="text/javascript" src="<?= \Base\BaseApp::getBaseLink() ?>Styles/js/Emoji.js"></script>
    <?php
    if(isset($currentRoute)) {
        if($currentRoute->getTitle() === true) { ?>
            <title><?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?></title>
        <?php } else { ?>
            <title><?= $currentRoute->getTitle() ?></title>
        <?php }
    } else { ?>
        <title><!--TITLE--></title>
    <?php } ?>
</head>
<body id="template-<?= $currentRoute !== null ?  $currentRoute->getContext() : 'error' ?>">
<header class="header-content">
    <?php include 'header.php'; ?>
</header>
<main class="main-content">
    <div class="p-main">
        <?php
        $runRouter = $app->runRouter($breadcrumb);
        if($runRouter instanceof Base\Reply\Redirect) {
            header('location: ' . $runRouter->getUrl());
        } ?>
    </div>
    <div class="get-error">
        <div class="overlay">
            <div class="overlay-title">
                <a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="Close"></a>Oops! We ran into some problems.
            </div>
            <div class="overlay-content">
                <div class="blockMessage">

                </div>
            </div>
        </div>
    </div>
    <div class="get-delete">
        <div class="overlay">
            <div class="overlay-title">
                <a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="Close"></a>
                Confirmer l'action
            </div>
            <div class="overlay-content">
            </div>
        </div>
    </div>
    <div class="get-form">
        <div class="overlay">
            <div class="overlay-title">
                <a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="Close"></a>
                <span class="popup-title"></span>
            </div>
            <div class="overlay-content">
            </div>
        </div>
    </div>
    <div class="get-view">
        <div class="overlay">
            <div class="overlay-title">
                <a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="Close"></a>
                <span class="popup-title"></span>
            </div>
            <div class="overlay-content">
                <div class="blockMessage">

                </div>
            </div>
        </div>
    </div>
<!--    --><?php //include 'mainAdmin.php' ?>
</main>
<footer class="footer-content">
    <?php include 'src/Template/Pub/footer.php'?>
</footer>
</body>
</html>