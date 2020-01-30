<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/28
 * Time: 13:25
 */

namespace Controllers;


abstract class ControllerBase
{
    abstract protected function onCreate();

    public function __construct()
    {
        $this->onCreate();
    }
}