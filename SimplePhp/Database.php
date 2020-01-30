<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:16
 */

namespace SimplePhp;


class Database
{
    private $driver;

    /**
     * Database constructor.
     * Database([args1,args2,*])
     * args[0]:set driver
     * args[1]:set database
     */
    public function __construct()
    {
        $args = func_get_args();
        switch (sizeof($args)) {
            case 1:
                $this->Driver($args[0]);
                break;
            case 2:
                $this->Driver($args[0]);
                $this->Database($args[1]);
                break;
        }
    }

    /**
     * @param $driver
     * @return $this
     * @throws \ReflectionException
     */
    public function Driver($driver)
    {
        $class = new \ReflectionClass("Drivers\\$driver");
        $instance = $class->newInstance();
        $this->driver = $instance;
        return $this;
    }

    /**
     * @param $database
     * @return $this
     */
    public function Database($database)
    {
        $this->driver->setDatabase($database);
        return $this;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function Collection($collection)
    {
        $this->driver->setCollection($collection);
        return $this->driver;
    }
}