<?php
/**
 * Created by PhpStorm.
 * User: Crxzy
 * Date: 2020/2/18
 * Time: 14:14
 */

namespace SimplePhp;


class Image
{
    //$filepath图片路径,$new_width新的宽度,$new_height新的高度
    const CROP_MODE_CENTER = 0;
    const CROP_MODE_START = 1;

    static public function pressImage($file_string, $new_width = 0, $new_height = 0, $crop_mode = self::CROP_MODE_CENTER)
    {
        if ($new_width == 0 && $new_height == 0) {
            return imagecreatefromstring($file_string);
        }
        $source_info = getimagesizefromstring($file_string);
        $source_width = $source_info[0];
        $source_height = $source_info[1];
        $source_ratio = $source_height / $source_width;
        if ($new_width == 0) {
            $new_width = $new_height / $source_ratio;
        }
        if ($new_height == 0) {
            $new_height = $new_width * $source_ratio;
        }
        $target_ratio = $new_height / $new_width;
        // 源图过高
        if ($source_ratio > $target_ratio) {
            $cropped_width = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            if ($crop_mode == self::CROP_MODE_CENTER) {
                $source_y = ($source_height - $cropped_height) / 2;
            } else if ($crop_mode == self::CROP_MODE_START) {
                $source_y = 0;
            } else {
                $source_y = ($source_height - $cropped_height) / 2;
            }
        } // 源图过宽
        elseif ($source_ratio < $target_ratio) {
            $cropped_width = $source_height / $target_ratio;
            $cropped_height = $source_height;
            if ($crop_mode == self::CROP_MODE_CENTER) {
                $source_x = ($source_width - $cropped_width) / 2;
            } else if ($crop_mode == self::CROP_MODE_START) {
                $source_x = 0;
            } else {
                $source_x = ($source_width - $cropped_width) / 2;
            }
            $source_y = 0;
        } // 源图适中
        else {
            $cropped_width = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }
        $source_image = imagecreatefromstring($file_string);
        $target_image = imagecreatetruecolor($new_width, $new_height);
        $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
        // 裁剪
        imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
        // 缩放
        imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $new_width, $new_height, $cropped_width, $cropped_height);
        imagedestroy($source_image);
        imagedestroy($cropped_image);
        return $target_image;
    }
}