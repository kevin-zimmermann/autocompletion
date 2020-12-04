<?php

ob_start();
session_start();
//session_destroy();
include 'src/Base/Base.php';
$params = [];
$error = null;
$PageNave = null;
$app = new Base\App('Pub');

$app->addRouter($app->getRoutersByType());
$currentRoute = $app->currentRoute();
$breadcrumb = new \Base\Util\Breadcrumbs($app, $currentRoute);
if($currentRoute)
{
    $_SESSION['currentPage'] = $currentRoute->getContext();
}
else
{
    $_SESSION['currentPage'] = 'error';
}
$runRouter = null;
if(!$BaseApp->request()->isXhr())
{
    include 'src/Template/Pub/PAGE_CONTENT.php';
}

if($runRouter == null)
{
    if($BaseApp->request()->isXhr())
    {
        $runRouter = $app->runRouter($breadcrumb);
        if(isset($runRouter['type']) && isset($runRouter['for']) &&
            $runRouter['type'] === 'json')
        {
            if($runRouter['for'] === 'error')
            {
                ob_end_clean ();
                echo json_encode([
                    'error' => $runRouter['error'],
                    'link'  => $runRouter['link']
                ]);
            }
            elseif($runRouter['for'] === 'deleted')
            {

                ob_end_clean ();
                echo json_encode([
                    'html' => $runRouter['html']
                ]);
            }
            elseif ($runRouter['for'] === 'form')
            {
                ob_end_clean ();
                echo json_encode((array)$runRouter['data']);
            }
        }
        if(isset($runRouter['type']) && $runRouter['type'] == 'view')
        {
            echo json_encode((array)$runRouter['data']);
        }
    }
}
