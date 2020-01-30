<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 22:07
 */

namespace Models;

abstract class DBModel
{
    protected $connect;
    protected $driver;

    public function __construct()
    {
        $this->onCreate();
        $this->setDriver();
        $this->connect = new \SimplePhp\Database($this->getDriver());
    }

    abstract protected function setDriver();

    abstract protected function onCreate();

    private function getDriver(): string
    {
        return $this->driver;
    }
}