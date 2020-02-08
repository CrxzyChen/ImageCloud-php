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
    protected function onCreate()
    {
        $this->ic = new ImageCloud();
        // TODO: Implement onCreate() method.
    }

    public function Index()
    {
        return array("message" => "connect success!");
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