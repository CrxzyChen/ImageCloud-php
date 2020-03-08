<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/3/6
 * Time: 16:18
 */

namespace SimplePhp;


use Models\CheckerModel;

class Gate
{
    private $checkers = array();
    private $class;
    private $method;
    private $parameters;
    private $last_error;

    public function __construct(&$class, &$method, &$parameters)
    {
        $this->class = $class;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function addChecker(CheckerModel $checker)
    {
        $this->checkers[] = $checker;
    }

    public function check(): bool
    {
        foreach ($this->checkers as $checker) {
            if ($checker->judge($this->class, $this->method, $this->parameters)) {
                $checker->success();
            } else {
                $error = $checker->false();
                if (is_string($this->last_error)) {
                    $this->last_error = $error;
                } else {
                    $this->last_error = json_encode($error);
                }
                return false;
            }
        }
        return true;
    }

    public function getLastError()
    {
        return $this->last_error;
    }
}