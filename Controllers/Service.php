<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/28
 * Time: 13:26
 */

namespace Controllers;

use Models\ImageCloud;

/**
 * @property \Models\ImageCloud ic
 */
class Service extends ControllerBase
{
    /**
     * @throws \ReflectionException
     * @throws \SimplePhp\Exception
     */
    protected function onCreate()
    {
        $this->ic = new ImageCloud();
        // TODO: Implement onCreate() method.
    }

    public function Index()
    {
        return array("message" => "connect success!");
    }

    public function getThumbInfo()
    {
        if (isset($_GET["thumb_id"])) {
            return $this->ic->getThumbInfo($_GET["thumb_id"]);
        } else {
            throw new \SimplePhp\Exception("less necessary parameter!");
        }
    }

    public function Download()
    {
        if (isset($_GET["thumb_id"])) {
            return $this->ic->downloadResource($_GET["thumb_id"]);
        }
    }

    public function DownloadStatus()
    {
        return $this->ic->getDownloadStatus($_GET["task_id"]);
    }
}