<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/1/14
 * Time: 11:37
 */

namespace Models;


use MongoDB\BSON\ObjectId;

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

    public function getDownloadStatus($task_id)
    {
        $downloader = $this->connect->Database("image_cloud")->Collection("downloader");
        $task = $downloader->find_one(array("_id" => new ObjectId($task_id)));
        if ($task != null) {
            return array("status" => "uncompleted", "count" => $task->count, "remain" => sizeof($task->targets));
        } else {
            return array("status" => "completed");
        }
    }

    /**
     * @param $url
     * @param $handle
     * @return bool
     */
    public function testUrl($url): bool
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);//设置超时时间
        curl_exec($handle);
        //检查是否404（网页找不到）
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($httpCode == 404) {
            return false;
        } else {
            return true;
        }
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

    /**
     * @param $thumb_id
     * @param $image_name
     * @param string $image_form
     * @return resource
     * @throws \SimplePhp\Exception
     */
    public function getImageResource($thumb_id, $image_name, $image_form = "jpg")
    {
        $image_pool = $this->connect->Database("image_cloud")->Collection("image_pool");
        $result = $image_pool->find_one(array("thumb_id" => (int)$thumb_id));
        if (null != ($name = $this->getImageName($image_name, $image_form, $result))) {
            $image_name = $name;
        } else {
            throw new \SimplePhp\Exception("error: $image_name.$image_form is not exist!");
        }
        $local_url = "http://{$this->config->host}:{$this->config->port}" . DIRECTORY_SEPARATOR . $this->config->root . DIRECTORY_SEPARATOR . $result->thumb_path . DIRECTORY_SEPARATOR . $image_name;
        $source_url = $result->source[0] . DIRECTORY_SEPARATOR . $image_name;
        if ($this->testUrl($local_url)) {
            $image = file_get_contents($local_url);
        } else {
            $image = file_get_contents($source_url);
        }

        return imagecreatefromstring($image);
    }

    public function downloadResource($thumb_id)
    {
        exec("python Scripts/index.py  --download $thumb_id", $task_id, $return_var);
        pclose(popen("python Scripts/index.py --start $task_id[0] &", 'r'));
        return $task_id;
    }

    protected function onInitial()
    {
        // TODO: Implement onInitial() method.
    }
}