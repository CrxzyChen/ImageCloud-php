<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/14
 * Time: 11:37
 */

namespace Models;


class ImageCloud extends DBModel
{
    private $config;

    /**
     * @param $name
     * @param $form
     * @param $result
     * @return string|null, Judge image name is exist, if not, judge other form have same name is exist, if not return null, if yes return image name
     */
    private function getImageName($name, $form, $result)
    {
        $image_name = null;
        foreach ($result->image_names as $value) {
            if ("$name.$form" == $value) {
                return $value;
            } else if ($name == implode('.', array_slice(explode('.', $value), 0, -1))) {
                $image_name = $value;
            }
        }
        return $image_name;
    }

    protected function onCreate()
    {
        $this->config = \SimplePhp\Config::get('image_server');
    }

    protected function setDriver()
    {
        $this->driver = "MongoDB";
        // TODO: Implement setDriver() method.
    }

    public function getImageResource($thumb_id, $image_name, $image_form = "jpg")
    {
        $image_pool = $this->connect->Database("image_cloud")->Collection("image_pool");
        $result = $image_pool->find_one(array("thumb_id" => (int)$thumb_id));
        if (null != ($name = $this->getImageName($image_name, $image_form, $result))) {
            $image_name = $name;
        } else {
            throw new \SimplePhp\Exception("error: $image_name.$image_form is not exist!");
        }
        $image = file_get_contents("http://{$this->config->host}:{$this->config->port}" .DIRECTORY_SEPARATOR. $this->config->root . DIRECTORY_SEPARATOR . $result->thumb_path . DIRECTORY_SEPARATOR . $image_name);
        return imagecreatefromstring($image);
    }
}