<?php


namespace Base;


class Repository
{
    /**
     * @param $shortName
     * @return \Base\Mvc\Entity\Entity
     */
    public function create($shortName)
    {
        return BaseApp::create($shortName);
    }
    /**
     * @param $shortName
     * @return \Base\Mvc\Entity\Finder
     */
    public function finder($shortName)
    {
        return BaseApp::finder($shortName);
    }
}