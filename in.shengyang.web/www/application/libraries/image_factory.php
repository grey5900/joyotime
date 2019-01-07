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

	function resizeImage($image,$width,$height,$scale) {
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = @imagecreatetruecolor($newImageWidth,$newImageHeight);
		//使用白色填充
		$white = @imagecolorallocate($newImage, 255, 255, 255);
		@imagefill($newImage, 0, 0, $white);
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
			default :
				return "Image not supportted !";
				//$source = '';
				break;
		}
		@imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

		switch($imageType) {
			case "image/gif":
				imagegif($newImage,$image);
				break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				imagejpeg($newImage,$image,90);
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);
				break;
		}

		chmod($image, 0777);
		return $image;
	}

	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale ,$start_x = 0 ,$start_y = 0 , $orignal_w = 0,$orignal_h = 0){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);

		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = @imagecreatetruecolor($newImageWidth,$newImageHeight);
		$white = @imagecolorallocate($newImage, 255, 255, 255);
		@imagefill($newImage, 0, 0, $white);
		//使用白色填充
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
			default :
				return "Image not supportted !";
				//$source = '';
				break;
		}
		if(!$source){
			return $image;
		}else{
			//如果是头像，把小图放在中间
			@imagecopyresampled($newImage,$source,$start_x,$start_y,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
			if(($start_x || $start_y  ) && $orignal_w && $orignal_h)
			{
				//再次填充颜色
				$fill_x = 	($width / 2) + $start_x ;
				$fill_y = 	($height / 2) + $start_y ;
				@imagefill($newImage, $fill_x, $fill_y, $white);
			}
			switch($imageType) {
				case "image/gif":
					@imagegif($newImage,$thumb_image_name);
					break;
				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					@imagejpeg($newImage,$thumb_image_name,90);
					break;
				case "image/png":
				case "image/x-png":
					@imagepng($newImage,$thumb_image_name);
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