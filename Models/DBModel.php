<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:07
 */

namespace Models;

use SimplePhp\Exception;
use stdClass;

abstract class DBModel
{
    protected $connect;
    protected $db_config;
    protected $database;

    /**
     * DBModel constructor.
     * @throws Exception
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->connect = new \SimplePhp\Database($this->getConfig());
        $this->setDatabase($this->database);
        $this->connect->Database($this->database);
        $this->onCreate();
    }

    abstract protected function setDatabase(&$database);

    abstract protected function onCreate();

    /**
     * @return stdClass
     * @throws Exception
     */
    private function getConfig(): stdClass
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        try {
            $db_config = \SimplePhp\Config::get("db.$class[1]");
        } catch (Exception $e) {
            $db_config = \SimplePhp\Config::get("db.default");
        }
        return $db_config;
    }

    public function __get($name)
    {
        return $this->connect->Collection($name);
    }
}