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
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://www.baidu.com/");
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);

        curl_close($ch);
    }
}