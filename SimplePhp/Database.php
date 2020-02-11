<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:16
 */

namespace SimplePhp;


/**
 * Class Database
 * @package SimplePhp
 * @property \Drivers\DatabaseDriver $driver
 */
class Database
{
    /**
     * Database constructor.
     * Database([args1,args2,*])
     * @param $config
     * @throws \ReflectionException
     */
    public function __construct($config)
    {
        $class = new \ReflectionClass("Drivers\\$config->driver");
        $instance = $class->newInstance($config->username, $config->password, $config->host, $config->port);
        $this->driver = $instance;
    }

    /**
     * @param $database
     * @return \SimplePhp\Database
     */
    public function Database($database)
    {
        $this->driver->setDatabase($database);
        return $this;
    }

    /**
     * @param $collection
     * @return \Drivers\DatabaseDriver
     */
    public function Collection($collection)
    {
        $this->driver->setCollection($collection);
        return $this->driver;
    }
}