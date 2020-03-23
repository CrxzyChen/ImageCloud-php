<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/14
 * Time: 11:37
 */

namespace Models;


use Drivers\MongoDB;
use MongoDB\BSON\ObjectId;
use SimplePhp\Image;
use SimplePhp\Network;

/**
 * @property string driver
 * @property MongoDB image_pool
 */
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
            if ("$name . $form" == $value) {
                return $value;
            } else if ($name == implode('.', array_slice(explode('.', $value), 0, -1))) {
                $image_name = $value;
            }
        }
        return $image_name;
    }

    /**
     * @param $task_id
     * @return array
     */
    public function getDownloadStatus($task_id)
    {
        $downloader = $this->connect->Database("image_cloud")->Collection("downloader");
        $task = $downloader->findOne(array("_id" => new ObjectId($task_id)));
        if ($task != null) {
            return array("status" => "uncompleted", "count" => $task->count, "remain" => sizeof($task->targets));
        } else {
            return array("status" => "completed");
        }
    }

    protected function onCreate()
    {
        $this->config = \SimplePhp\Config::get('image_server');
    }

    /**
     * @param int $thumb_id
     * @param string $image_name
     * @param string $image_form
     * @param int $width
     * @param int $height
     * @return resource
     * @throws \SimplePhp\Exception
     */
    public function getImageResource(int $thumb_id, string $image_name, string $image_form = "jpg", int $width = 0, int $height = 0)
    {
        $image_pool = $this->connect->Database("image_cloud")->Collection("image_pool");
        $result = $image_pool->findOne(array("thumb_id" => $thumb_id), array());
        if (null != ($name = $this->getImageName($image_name, $image_form, $result))) {
            $image_name = $name;
        } else {
            throw new \SimplePhp\Exception("error: $image_name.$image_form is not exist!");
        }
        $local_url = "http://{$this->config->host}:{$this->config->port}" . DIRECTORY_SEPARATOR . $this->config->root . DIRECTORY_SEPARATOR . $result->thumb_path . DIRECTORY_SEPARATOR . $image_name;
        $source_url = $result->source[0] . DIRECTORY_SEPARATOR . $image_name;
        if (Network::checkConnect($local_url)) {
            $image_string = file_get_contents($local_url);
        } else {
            $image_string = file_get_contents($source_url);
        }
        return Image::pressImage($image_string, $width, $height, IMAGE::CROP_MODE_START);
    }


    public function downloadResource($thumb_id)
    {
        exec("python Scripts/index.py  --download $thumb_id", $task_id, $return_var);
        pclose(popen("python Scripts/index.py --start $task_id[0] &", 'r'));
        return $task_id;
    }

    /**
     * @param int $thumb_id
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getThumbInfo(int $thumb_id)
    {
        return $this->image_pool->findOne(array("thumb_id" => $thumb_id));
    }

    protected function setDatabase(&$database)
    {
        $database = "image_cloud";
        // TODO: Implement setDatabase() method.
    }
}