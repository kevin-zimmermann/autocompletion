<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin login </title>
    <!--SCRIPT-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $app->getBaseLink() ?>Styles/css/fa.css">
    <link rel="stylesheet" href="<?= $app->getBaseLink() ?>Styles/css/admin.css" type="text/css"/>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxDelete.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxForm.js"></script>
    <script type="text/javascript"  src="<?= $app->getBaseLink() ?>Styles/js/AjaxError.js"></script>
</head>
<body id="template-<?= isset($currentRoute) ? $currentRoute->getContext() : 'error' ?>">
<header>

</header>
<main>
    <?php
    $runRouter = $app->runRouter($breadcrumb);
    if ($runRouter instanceof Base\Reply\Redirect) {
        header('location: ' . $runRouter->getUrl());
    }
    ?>
</main>
</body>
</html>