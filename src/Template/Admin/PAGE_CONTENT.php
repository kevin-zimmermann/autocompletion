<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $app->getBaseLink() ?>Styles/css/fa.css">
    <link rel="stylesheet" href="<?= $app->getBaseLink() ?>Styles/css/admin.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/canvas-menu-admin.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxDelete.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxForm.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxError.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/Editor/editorButton.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/Editor/editor.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/Input.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxInfo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/pikaday.jquery.js"></script>
    <?php
    if(isset($currentRoute)) {
        if($currentRoute->getTitle() === true) { ?>
            <title><?= \Base\BaseApp::getConfigOptions()->defaultNameSite ?></title>
        <?php } else { ?>
            <title><?= $currentRoute->getTitle() ?></title>
        <?php }
    } else { ?>
        <title> <!--TITLE--></title>
    <?php } ?>

</head>
<body id="template-<?= $currentRoute !== null ?  $currentRoute->getContext() : 'error' ?>">
<header class="header-content">
    <?php include 'src/template/Admin/header.php';?>
</header>
<main>
    <?php include 'mainAdmin.php'?>
    <div class="p-main">
        <?php
        $runRouter = $app->runRouter($breadcrumb);
        if($runRouter instanceof Base\Reply\Redirect) {?>
            <script>location.replace("<?= $runRouter->getUrl() ?>"); </script>
        <?php } ?>
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
    <div class="get-info">
        <div class="overlay">
            <div class="overlay-title">
                <a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="Close"></a>
                <span class="popup-title"></span>
            </div>
            <div class="overlay-content">
                <div class="block-formRow">
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="footer-content">
</footer>
</body>
</html>
