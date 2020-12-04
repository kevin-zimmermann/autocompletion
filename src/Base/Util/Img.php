<?php


namespace Base\Util;


use Base\BaseApp;

class Img
{
    public static function getImg($entity, $entityValue, $entityClass, $keyPhrase, $size = "m", $href = "")
    {

        if(empty($href))
        {
            $tag = "span";
            $endTag = 'span';
        }
        else{
            $tag = 'a href="' . $href . '"';
            $endTag = 'a';
        }
        if(!$entity->{$entityValue})
        {
            return '<' . $tag .' class="avatar avatar--' . $size . ' avatar--' . $entityClass . '"><span></span><span class="u-srOnly">' . BaseApp::phrase($keyPhrase) . '</span></' . $endTag .'>';
        }
        $src = $entity->getUrlBypath();
        return '<' . $tag .' class="avatar avatar--' . $size . ' "><img src="' . $src . '" alt="' . $entity->getAlt() . '"></' . $endTag .'>';
    }
}