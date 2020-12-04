<?php

namespace Base\Cron;

use Base\BaseApp;
use Base\Repository;

abstract class AbstractCron
{
    /**
     * @var string
     */
    protected $shortName;
    /**
     * @var string
     */
    protected $className;

    /**
     * AbstractCron constructor.
     * @param $shortName
     * @param $className
     */
    public function __construct($shortName, $className)
    {
        $this->shortName = $shortName;
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    abstract protected function Entity();

    /**
     * @return mixed
     */
    abstract protected function Where();

    /**
     * @return mixed
     */
    abstract protected function limit();

    /**
     * @return mixed
     */
    abstract protected function type();

    /**
     * @return mixed
     */
    abstract protected function execute();

    /**
     * @return mixed
     */
    abstract protected function algorithmRun();


    /**
     * @return bool|string
     * @throws \Exception
     */
	public function run()
    {
        try {
            $this->algorithmRun();
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
        if($this->execute())
        {
            $entities = $this->finder($this->Entity());
            if($this->Where())
            {
                $entities->where($this->Where());
            }
            if(!$this->limit())
            {
                $entities->limit(BaseApp::getConfigOptions()->limitMaxByPage);
            }
            else
            {
                $entities->limit($this->limit());
            }
            $entities = $entities->fetch();
            if(!empty($entities))
            {
                foreach ($entities as $entity)
                {
                    $type = $this->type();
                    if($type == 'delete')
                    {
                        $entity->delete();
                    }
                }
            }

        }
        return true;
    }

    /**
     * @param $shortName
     * @return \Base\Mvc\Entity\Finder
     */
    public function finder($shortName)
    {
        return BaseApp::finder($shortName);
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function getEntities()
    {
        $entities = $this->finder($this->Entity());
        if($this->Where())
        {
            $entities->where($this->Where());
        }
        if(!$this->limit())
        {
            $entities->limit(BaseApp::getConfigOptions()->limitMaxByPage);
        }
        else
        {
            $entities->limit($this->limit());
        }
        return $entities->fetch();
    }

    /**
     * @return \Base\Mvc\Entity\Entity|null
     * @throws \Exception
     */
    public function getEntity()
    {
        $entities = $this->finder($this->Entity());
        if($this->Where())
        {
            $entities->where($this->Where());
        }
        if(!$this->limit())
        {
            $entities->limit(BaseApp::getConfigOptions()->limitMaxByPage);
        }
        else
        {
            $entities->limit($this->limit());
        }
        return $entities->fetchOne();
    }

    /**
     * @param $shortName
     * @return \Base\Mvc\Entity\Entity
     */
    protected function create($shortName)
    {
        return BaseApp::create($shortName);
    }

    /**
     * @param $shortName
     * @return Repository
     */
    protected function repository($shortName)
    {
        $className = BaseApp::stringToClass($shortName, '%s\Repository\%s');
        return BaseApp::setNewClass($className);
    }
}