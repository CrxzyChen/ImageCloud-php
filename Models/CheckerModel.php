<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/3/7
 * Time: 23:06
 */

namespace Models;


interface CheckerModel
{
    public function judge(&$class, &$method, &$parameters): bool;

    public function success();

    public function false();
}