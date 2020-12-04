<?php
namespace App;

class Permission
{
    public static function getCustomTitle($user)
    {
        if($user->custom_title)
        {
            return $user->custom_title;
        }
        else
        {
            if($user->is_admin)
            {
                return \Base\BaseApp::phrase('admin');
            }
            elseif ($user->is_modo)
            {
                return \Base\BaseApp::phrase('modo');
            }
            else
            {
                return \Base\BaseApp::phrase('member');
            }
        }
    }
    public static function getPermission($user)
    {
        if (empty($user))
        {
            return false;
        }
        if($user->is_admin || $user->is_modo)
        {
            return true;
        }
        return false;
    }
}