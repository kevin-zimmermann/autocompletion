<?php
ob_start();
session_start();
include 'src/Base/Base.php';
$params = [];
$error = null;
$PageNave = null;
$form = null;
$app = new Base\App('Admin');

$app->addRouter($app->getRoutersByType());
$currentRoute = $app->currentRoute();
$breadcrumb = new \Base\Util\Breadcrumbs($app, $currentRoute);
$runRouter = null;
if(!$BaseApp->request()->isXhr())
{
    if($currentRoute != null)
    {
        Base\BaseApp::request()->setSession('currentPage', $currentRoute->getContext());
        if(!$currentRoute->getNewPage())
        {
            include 'src/Template/Admin/PAGE_CONTENT.php';
        }
        else
        {
            include 'src/Template/Admin/newPage.php';
        }
    }
    else
    {
        Base\BaseApp::request()->setSession('currentPage', 'error');
        if(!\Base\BaseApp::VisitorAdmin())
        {
            header('location: ' . $app->buildLink('admin:login'));
        }
        else
        {
            Base\BaseApp::request()->setSession('is_admin', 1);
            include 'src/Template/Admin/PAGE_CONTENT.php';
        }
    }
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
            elseif ($runRouter['for'] === 'info')
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
