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
 * Class Gallery
 * @package Controllers
 * @property ImageCloud image_cloud
 */
class Gallery extends ControllerBase
{
    private $image_cloud;

    protected function onCreate()
    {
        $this->image_cloud = new \Models\ImageCloud();
        // TODO: Implement onCreate() method.
    }

    public function Index()
    {
        $image_resource = $this->image_cloud->getImageResource($_GET["gallery"], $_GET["image_name"], $_GET["image_form"]);
        return $image_resource;
    }
}