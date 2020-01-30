<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/10
 * Time: 23:32
 */

namespace Drivers;


interface DatabaseDriver
{
    public function find($query, $option);

    public function find_one($query);

    public function insert($document);

    public function delete($query, $option);

    public function save($document);

    public function setDatabase($database);

    public function setCollection($collection);
}