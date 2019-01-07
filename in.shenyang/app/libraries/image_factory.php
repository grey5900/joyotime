<?php
/**
 * 图片上传处理
 * Create by 2012-6-11
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Code
class Image_factory{
	var $cfg;
	var $allowed_image_ext;
	var $image_ext;

	function __construct(){
		$CI = &get_instance();
		$CI->config->load('config_image');
		$this->cfg = $CI->config->item('image_cfg');
		$this->allowed_image_ext = $this->cfg['allowed_image_ext'];
		$this->image_ext = implode(' ', $this->allowed_image_ext);
	}

	function resizeImage($image,$width,$height,$scale,$replace=true,$newname='') {
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image);
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image);
				break;
			case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image);
				break;
		}
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

		switch($imageType) {
			case "image/gif":
				$replace ? imagegif($newImage,$image) : imagegif($newImage,$newname) ;
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$replace ? imagejpeg($newImage,$image,90) : imagejpeg($newImage,$newname,90) ;
				break;
			case "image/png":
			case "image/x-png":
				$replace ? imagepng($newImage,$image) : imagepng($newImage,$newname) ;
				break;
		}

		chmod($replace ? $image : $newname, 0777);
		return $replace ? $image : $newname;
	}

	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);

		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=@imagecreatefromgif($image);
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		//		echo function_exists('imagecreatefromjpeg')?'有':'没有';exit;
				$source=@imagecreatefromjpeg($image);
				break;
			case "image/png":
			case "image/x-png":
				$source=@imagecreatefrompng($image);
				break;
		}
		if(!$source){
			return $image;
		}else{
			imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
			switch($imageType) {
				case "image/gif":
					imagegif($newImage,$thumb_image_name);
					break;
				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					imagejpeg($newImage,$thumb_image_name,90);
					break;
				case "image/png":
				case "image/x-png":
					imagepng($newImage,$thumb_image_name);
					break;
			}
			chmod($thumb_image_name, 0777);
			return $thumb_image_name;
		}
	}
	//You do not need to alter these functions
	function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}
	//You do not need to alter these functions
	function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}

}

// File end