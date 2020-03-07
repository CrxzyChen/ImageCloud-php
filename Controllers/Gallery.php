<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/28
 * Time: 13:26
 */

namespace Controllers;


/**
 * Class Gallery
 * @package Controllers
 * @property \Models\ImageCloud image_cloud
 */
class Gallery extends ControllerBase
{
    private $image_cloud;

    protected function onCreate()
    {
        $this->image_cloud = new \Models\ImageCloud();
        // TODO: Implement onCreate() method.
    }

    /**
     * @return resource
     * @throws \SimplePhp\Exception
     */
    public function Index()
    {
        if (isset($_GET["gallery"]) && isset($_GET["image_name"]) && isset($_GET["image_form"])) {
            $height = isset($_GET["height"]) ? $_GET["height"] : 0;
            $width = isset($_GET["width"]) ? $_GET["width"] : 0;
            $image_resource = $this->image_cloud->getImageResource($_GET["gallery"], $_GET["image_name"], $_GET["image_form"], $width, $height);
            return $image_resource;
        }
    }
}