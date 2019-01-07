<?php
/**
 * 图片处理
 * Create by 2012-3-14
 * @author liuw
 * @copyright Copyright(c) 2012-2014
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Image{
	var $cfg;
	var $CI;
	var $suffix;
	var $jpegQuality;
	var $jpegInterlace;
	var $savePath;
	var $viewUri;
	var $def_red;
	var $def_green;
	var $def_blue;
	var $png_alpha;
	
	function __construct(){
		$this->CI = &get_instance();
		//获取图片参数
		$this->CI->config->load('image_config');
		$this->cfg = $this->CI->config->item('thumb');
		$this->suffix = $this->cfg['suffix'];
		$this->jpegQuality = $this->cfg['jpeg_quality'];
		$this->jpegInterlace = $this->cfg['jpeg_interlace'];
		$this->savePath = $this->cfg['save_path'];
		$this->viewUri = $this->cfg['view_uri'];
		unset($this->cfg['suffix'],$this->cfg['jpeg_quality'],$this->cfg['jpeg_interlace'],$this->cfg['save_path'], $this->cfg['view_uri']);
		$this->def_red = $this->CI->config->item('background_red');
		$this->def_green = $this->CI->config->item('background_green');
		$this->def_blue = $this->CI->config->item('background_blue');
		$this->png_alpha = $this->CI->config->item('png_alpha');
	}
	
	/**
	 * 图片处理
	 * Create by 2012-3-14
	 * @author liuw
	 * @param string $src
	 * @param string $uid
	 * @return mixed
	 */
	public function compress($src, $uid){
		foreach($this->cfg as $key=>$value){
			$dst = $this->savePath.str_replace("#uid#",$uid,$value['folder']);
			$this->mkdir($dst);
			//获得文件名
			$pathinfo = pathinfo($src);
			$dst .= str_replace('.',$this->suffix.'.',$pathinfo['basename']);
			echo $this->thumb($src, $dst, $value['w'], $value['h']).'<br/>';
		}
	}
	
	/**
	 * 创建目录
	 * Create by 2012-3-14
	 * @author liuw
	 * @param unknown_type $dir
	 */
	private function mkdir($dir){
		$dirs = explode('/', $dir);
		if(isset($dirs) && !empty($dirs)){
			$create = '';
			foreach($dirs as $d){
				if(!empty($d)){
					$create .= '/'.$d;
					if(!is_dir($create)){
						@mkdir($create, 0777);
					}
				}
			}
		}
	}
	
	/**
	 * 缩放并裁切图片
	 * Create by 2012-3-14
	 * @author liuw
	 * @param string $src 原始图片地址
	 * @param string $dst 缩略图片地址
	 * @param int $dst_w 缩略图宽度
	 * @param int $dst_h 缩略图高度
	 * @param boolean $create_thumb 是否生成缩略图
	 * @return mixed，返回缩略图访问url
	 */
	private function thumb($src, $dst, $dst_w=120, $dst_h=90){
		$info = $this->get_info($src);
		if($info !== FALSE){
			$src_w = $info['width'];
			$src_h = $info['height'];
			$pathinfo = pathinfo($src);
			$type = $pathinfo['extension'];
			$type = empty($type) ? $info['type'] : $type;
			$type = strtolower($type);
			$interlace = $this->interlace ? 1 : 0;
			unset($info);
			//计算缩放比例
			$mid_w = $mid_h = 0;
			if($src_w < $src_h){
				$mid_w = $dst_w;
				$mid_h = ceil($src_h * (floatval($dst_w)/$src_w));
			}else{
				$mid_h = $dst_h;
				$mid_w = ceil($src_w * (floatval($dst_h)/$src_h));
			}
			
			//计算裁切点
			$dst_x = floor(abs($mid_w - $dst_w) / 2);
			$dst_y = floor(abs($mid_h - $dst_h) / 2);
			
			echo $dst_x.":".$dst_y.'<p/><p/>';
			
			$srcImg = $this->get_image($src, $type);
			//创建缩放图对象
			$thumb = $type !== 'gif' && function_exists('imagecreatetruecolor') ? imagecreatetruecolor($mid_w, $mid_h) : imagecreate($mid_w, $mid_h);
			//缩略图对象
			$dstImg = $type !== 'gif' && function_exists('imagecreatetruecolor') ? imagecreatetruecolor($dst_w, $dst_h) : imagecreate($dst_w, $dst_h);
			//缩放图片
			if(function_exists('imagecopyresampled')){
				imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $mid_w, $mid_h, $src_w, $src_h);
				//裁切
				imagecopyresampled($dstImg, $thumb, 0, 0, $dst_x, $dst_y, $dst_w, $dst_h, $dst_w, $dst_h);
			}else{
				imagecopyresized($thumb, $srcImg, 0, 0, 0, 0, $mid_w, $mid_h, $src_w, $src_h);
				//裁切
				imagecopyresized($dstImg, $thumb, 0, 0, $dst_x, $dst_y, $dst_w, $dst_h, $dst_w, $dst_h);
			}
			
			if(in_array($type, array('gif', 'png'))){
				//gif和png图片设置背景色
				if($type === 'gif')
					$bg_color = imagecolorallocate($dstImg, $this->def_red, $this->def_green, $this->def_blue);
				elseif($type === 'png')
					$bg_color = imagecolorallocatealphpa($dstImg, $this->def_red, $this->def_green, $this->def_blue, $this->png_alpha);
				imagecolortransparent($dstImg, $bg_color);
			}
			//设置jpg图片隔行扫描
			if(in_array($type, array('jpg', 'jpeg'))){
				imageinterlace($dstImg, $interlace);
			}
			//保存缩略图
			list($dirname, $basename, $extension) = pathinfo($dst);
			$th = str_replace('.','_t.', $dst);
			$fun = 'image'.(in_array($type, array('jpg', 'jpeg')) ? 'jpeg' : $type);
			if(in_array($type, array('jpg', 'jpeg'))){
				$fun($dstImg, $dst, $this->jpegQuality);
				$fun($thumb, $th, $this->jpegQuality);
			}else{ 
				$fun($dstImg, $dst);
				$fun($thumb, $th);
			}
			imagedestroy($dstImg);
			imagedestroy($thumb);
			imagedestroy($srcImg);
			return $this->viewUri.$basename;
		}
	}
	
	/**
	 * 获取图像信息
	 * Create by 2012-3-14
	 * @author liuw
	 * @param string $imagePath
	 * @return mixed array(width, height, type, size, mime)
	 */
	private function get_info($imagePath){
		$imageInfo = getimagesize($imagePath);
		if($imageInfo !== FALSE){
			$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
			$imageSize = filesize($img);
			$info = array (
				"width" => $imageInfo[0],
				"height" => $imageInfo[1],
				"type" => $imageType,
				"size" => $imageSize,
				"mime" => $imageInfo['mime']
			);
			$pathinfo = pathinfo($img);
			$type = $pathinfo['extension'];
			$info['type'] = empty ($type) ? $info['type'] : $type;
			return $info;
		}else
			return FALSE;
	}
	
	/**
	 * 获取图像对象
	 * Create by 2012-3-14
	 * @author liuw
	 * @param string $imagePath
	 * @param string $type
	 * @return mixed
	 */
	private function get_image($imagePath, $type){
		$createFun = 'imagecreatefrom'.(in_array($type, array('jpg','jpeg'))?'jpeg':$type);
		return $createFun($imagePath);
	}

	/**
		* PHP图片水印 (水印支持图片或文字) 
		* 参数： 
		*      $groundImage    背景图片，即需要加水印的图片，暂只支持GIF,JPG,PNG格式； 
		*      $waterPos        水印位置，有10种状态，0为随机位置； 
		*                        1为顶端居左，2为顶端居中，3为顶端居右； 
		*                        4为中部居左，5为中部居中，6为中部居右； 
		*                        7为底端居左，8为底端居中，9为底端居右； 
		*      $waterImage        图片水印，即作为水印的图片，暂只支持GIF,JPG,PNG格式； 
		*      $waterText        文字水印，即把文字作为为水印，支持ASCII码，不支持中文； 
		*      $textFont        文字大小，值为1、2、3、4或5，默认为5； 
		*      $textColor        文字颜色，值为十六进制颜色值，默认为#FF0000(红色)； 
		*      $suffix     生成水印图片的后缀
		*      $filename  生成的水印图片
		* 
		* 注意：Support GD 2.0，Support FreeType、GIF Read、GIF Create、JPG 、PNG 
		*      $waterImage 和 $waterText 最好不要同时使用，选其中之一即可，优先使用 $waterImage。 
		*      当$waterImage有效时，参数$waterString、$stringFont、$stringColor均不生效。 
		*      加水印后的图片的文件名和 $groundImage 一样。 
		*/
	public function watermark($groundImage, $waterPos = 0, $waterImage = "", $waterText = "", $textFont = 5, $textColor = "#FF0000", $suffix = 'wthumb', $filename='') {
		$isWaterImage = false;
		$formatMsg = "暂不支持该文件格式，请用图片处理软件将图片转换为GIF、JPG、PNG格式。";
	
		//读取水印文件 
		if (!empty ($waterImage) && file_exists($waterImage)) {
			$isWaterImage = true;
			$water_info = getimagesize($waterImage);
			$water_w = $water_info[0]; //取得水印图片的宽 
			$water_h = $water_info[1]; //取得水印图片的高
	
			//取得水印图片的格式 
			switch ($water_info[2]) {
				case 1 :
					$water_im = imagecreatefromgif($waterImage);
					break;
				case 2 :
					$water_im = imagecreatefromjpeg($waterImage);
					break;
				case 3 :
					$water_im = imagecreatefrompng($waterImage);
					break;
				default :
					die($formatMsg);
			}
		}
	
		//读取背景图片 
		if (!empty ($groundImage) && file_exists($groundImage)) {
			$ground_info = getimagesize($groundImage);
			$ground_w = $ground_info[0]; //取得背景图片的宽 
			$ground_h = $ground_info[1]; //取得背景图片的高
			$type = '';
			//取得背景图片的格式 
			switch ($ground_info[2]) {
				case 1 :
					$ground_im = imagecreatefromgif($groundImage);
					// 判断是不是动画
					$fp = fopen($groundImage, 'rb');
					$filecontent = fread($fp, filesize($groundImage));
					fclose($fp);
					if(strpos($filecontent, 'NETSCAPE2.0') !== FALSE) $is_animation = true; 
					$type = 'gif';
					break;
				case 2 :
					$ground_im = imagecreatefromjpeg($groundImage);
					$type = 'jpg';
					break;
				case 3 :
					$ground_im = imagecreatefrompng($groundImage);
					$type = 'png';
					break;
				default :
					die($formatMsg);
			}
		} else {
			die("需要加水印的图片不存在！");
		}
		
		// 生成文件
		$filename = empty ($filename) ? substr($groundImage, 0, strlen($image)-strlen($type)) . $suffix . '.' . $type : $filename;
		if($is_animation) {
			// 动画就在这里处理
			copy($groundImage, $filename);
			imagedestroy($ground_im);
		}
	
		//水印位置 
		if ($isWaterImage) {
			//图片水印 
			$w = $water_w;
			$h = $water_h;
			$label = "图片的";
		} else	{
			//文字水印 
			$temp = imagettfbbox(ceil($textFont * 2.5), 0, "./1.ttf", $waterText); //取得使用 TrueType 字体的文本的范围 
			$w = $temp[2] - $temp[6];
			$h = $temp[3] - $temp[7];
			unset ($temp);
			$label = "文字区域";
		}
		if (($ground_w < $w) || ($ground_h < $h)) {
			echo "需要加水印的图片的长度或宽度比水印" . $label . "还小，无法生成水印！";
			return;
		}
		switch ($waterPos) {
			case 0 : //随机 
				$posX = rand(0, ($ground_w - $w));
				$posY = rand(0, ($ground_h - $h));
				break;
			case 1 : //1为顶端居左 
				$posX = 0;
				$posY = 0;
				break;
			case 2 : //2为顶端居中 
				$posX = ($ground_w - $w) / 2;
				$posY = 0;
				break;
			case 3 : //3为顶端居右 
				$posX = $ground_w - $w;
				$posY = 0;
				break;
			case 4 : //4为中部居左 
				$posX = 0;
				$posY = ($ground_h - $h) / 2;
				break;
			case 5 : //5为中部居中 
				$posX = ($ground_w - $w) / 2;
				$posY = ($ground_h - $h) / 2;
				break;
			case 6 : //6为中部居右 
				$posX = $ground_w - $w;
				$posY = ($ground_h - $h) / 2;
				break;
			case 7 : //7为底端居左 
				$posX = 0;
				$posY = $ground_h - $h;
				break;
			case 8 : //8为底端居中 
				$posX = ($ground_w - $w) / 2;
				$posY = $ground_h - $h;
				break;
			case 9 : //9为底端居右 
				$posX = $ground_w - $w;
				$posY = $ground_h - $h;
				break;
			default : //随机 
				$posX = rand(0, ($ground_w - $w));
				$posY = rand(0, ($ground_h - $h));
				break;
		}
	
		//设定图像的混色模式 
		imagealphablending($ground_im, true);
	
		if ($isWaterImage) {
			 //图片水印 
			imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h); //拷贝水印到目标文件         
		} else {
			//文字水印
			if (!empty ($textColor) && (strlen($textColor) == 7)) {
				$R = hexdec(substr($textColor, 1, 2));
				$G = hexdec(substr($textColor, 3, 2));
				$B = hexdec(substr($textColor, 5));
			} else {
				die("水印文字颜色格式不正确！");
			}
			imagestring($ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B));
		}
	
		//取得背景图片的格式 
		switch ($ground_info[2]) {
			case 1 :
				imagegif($ground_im, $filename);
				break;
			case 2 :
				imagejpeg($ground_im, $filename);
				break;
			case 3 :
				imagepng($ground_im, $filename);
				break;
			default :
				die("生成水印图片出错");
		}
	
		//释放内存 
		if (isset ($water_info))
			unset ($water_info);
		if (isset ($water_im))
			imagedestroy($water_im);
		unset ($ground_info);
		imagedestroy($ground_im);
		imagedestroy($filename);
	}
	
	/**
	 * 生成发光效果的文字水印
	 * @param $image_file 图片地址
	 * @param $size 字体大小
	 * @param $x X坐标
	 * @param $y Y坐标
	 * @param $suffix 生成的图片的后缀
	 * @param $filename 生成的文件名称 默认为空自动获取
	 * @param $color 颜色
	 * @param $fontfile 水印文字字体
	 * @param $text 水印文字
	 * @param $outer 发光的颜色
	 */
	public function imagetextouter($image_file, $text, $size, $x, $y, $suffix = 'sthumb', $filename = '', $color = '#FF0000', $outer = '#FFFFFF', $fontfile = 'simfang.ttf') {
		$info = get_image_info($image_file);
		if ($info) {
			$im = get_image($image_file);
			if (!function_exists('ImageColorAllocateHEX')) {
				function ImageColorAllocateHEX($im, $s) {
					if ($s {
						0 }
					== "#")
					$s = substr($s, 1);
					$bg_dec = hexdec($s);
					return imagecolorallocate($im, ($bg_dec & 0xFF0000) >> 16, ($bg_dec & 0x00FF00) >> 8, ($bg_dec & 0x0000FF));
				}
			}
	
			$ttf = false;
	
			if (is_file($fontfile)) {
				$ttf = true;
				$area = imagettfbbox($size, 0, $fontfile, $text);
	
				$width = $area[2] - $area[0] + 2;
				$height = $area[1] - $area[5] + 2;
			} else {
				$width = strlen($text) * 10;
				$height = 16;
			}
	
			$im_tmp = imagecreate($width, $height);
			$white = imagecolorallocate($im_tmp, 255, 255, 255);
			$black = imagecolorallocate($im_tmp, 0, 0, 0);
	
			$color = ImageColorAllocateHEX($im, $color);
			$outer = ImageColorAllocateHEX($im, $outer);
	
			if ($ttf) {
				imagettftext($im_tmp, $size, 0, 0, $height -2, $black, $fontfile, $text);
				imagettftext($im, $size, 0, $x, $y, $color, $fontfile, $text);
				$y = $y - $height +2;
			} else {
				imagestring($im_tmp, $size, 0, 0, $text, $black);
				imagestring($im, $size, $x, $y, $text, $color);
			}
	
			for ($i = 0; $i < $width; $i++) {
				for ($j = 0; $j < $height; $j++) {
					$c = ImageColorAt($im_tmp, $i, $j);
					if ($c !== $white) {
						imagecolorat($im_tmp, $i, $j -1) != $white || imagesetpixel($im, $x + $i, $y + $j -1, $outer);
						imagecolorat($im_tmp, $i, $j +1) != $white || imagesetpixel($im, $x + $i, $y + $j +1, $outer);
						imagecolorat($im_tmp, $i -1, $j) != $white || imagesetpixel($im, $x + $i -1, $y + $j, $outer);
						imagecolorat($im_tmp, $i +1, $j) != $white || imagesetpixel($im, $x + $i +1, $y + $j, $outer);
	
						// 取消注释，与Fireworks的发光效果相同
						/*
						imagecolorat ($im_tmp, $i - 1, $j - 1) != $white || imagesetpixel($im, $x + $i - 1, $y + $j - 1, $outer);
						imagecolorat ($im_tmp, $i + 1, $j - 1) != $white || imagesetpixel($im, $x + $i + 1, $y + $j - 1, $outer);
						imagecolorat ($im_tmp, $i - 1, $j + 1) != $white || imagesetpixel($im, $x + $i - 1, $y + $j + 1, $outer);
						imagecolorat ($im_tmp, $i + 1, $j + 1) != $white || imagesetpixel($im, $x + $i + 1, $y + $j + 1, $outer);
						*/
					}
				}
			}
			// 生成图片
			$imageFun = 'image' . ($info['type'] == 'jpg' ? 'jpeg' : $info['type']);
			$filename = empty ($filename) ? substr($image_file, 0, strlen($image_file)-strlen($info['type'])) . $suffix . '.' . $info['type'] : $filename;
	
			$imageFun ($im_tmp, $filename);
			imagedestroy($im_tmp);
			imagedestroy($im);
			return $filename;
		}
	
		return false;
	}

	/**
	 * 生成UPC-A条形码
	 * @param string $code 图像编码
	 * @param string $type 图像格式
	 * @param string $lw  单元宽度
	 * @param string $hi   条码高度
	 * @return string
	 */
	public function UPCA($code, $type = 'png', $lw = 2, $hi = 100) {
		$Lencode = array (
			'0001101',
			'0011001',
			'0010011',
			'0111101',
			'0100011',
			'0110001',
			'0101111',
			'0111011',
			'0110111',
			'0001011'
		);
		$Rencode = array (
			'1110010',
			'1100110',
			'1101100',
			'1000010',
			'1011100',
			'1001110',
			'1010000',
			'1000100',
			'1001000',
			'1110100'
		);
		$ends = '101';
		$center = '01010';
		/* UPC-A Must be 11 digits, we compute the checksum. */
		if (strlen($code) != 11) {
			die("UPC-A Must be 11 digits.");
		}
		/* Compute the EAN-13 Checksum digit */
		$ncode = '0' . $code;
		$even = 0;
		$odd = 0;
		for ($x = 0; $x < 12; $x++) {
			if ($x % 2) {
				$odd += $ncode[$x];
			} else {
				$even += $ncode[$x];
			}
		}
		$code .= (10 - (($odd * 3 + $even) % 10)) % 10;
		/* Create the bar encoding using a binary string */
		$bars = $ends;
		$bars .= $Lencode[$code[0]];
		for ($x = 1; $x < 6; $x++) {
			$bars .= $Lencode[$code[$x]];
		}
		$bars .= $center;
		for ($x = 6; $x < 12; $x++) {
			$bars .= $Rencode[$code[$x]];
		}
		$bars .= $ends;
		/* Generate the Barcode Image */
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($lw * 95 + 30, $hi +30);
		} else {
			$im = imagecreate($lw * 95 + 30, $hi +30);
		}
		$fg = imagecolorallocate($im, 0, 0, 0);
		$bg = imagecolorallocate($im, 255, 255, 255);
		imagefilledrectangle($im, 0, 0, $lw * 95 + 30, $hi +30, $bg);
		$shift = 10;
		for ($x = 0; $x < strlen($bars); $x++) {
			if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
				$sh = 10;
			} else {
				$sh = 0;
			}
			if ($bars[$x] == '1') {
				$color = $fg;
			} else {
				$color = $bg;
			}
			imagefilledrectangle($im, ($x * $lw) + 15, 5, ($x +1) * $lw +14, $hi +5 + $sh, $color);
		}
		/* Add the Human Readable Label */
		imagestring($im, 4, 5, $hi -5, $code[0], $fg);
		for ($x = 0; $x < 5; $x++) {
			ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi +5, $code[$x +1], $fg);
			ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi +5, $code[$x +6], $fg);
		}
		imagestring($im, 4, $lw * 95 + 17, $hi -5, $code[11], $fg);
		/* Output the Header and Content. */
		output_image($im, $type);
	}

	/**
	 * 显示出图片
	 *
	 * @param 图片的流 $im
	 * @param 图片类型 $type
	 */
	private function output_image($im, $type = 'png') {
		header("Content-type: image/" . $type);
		$ImageFun = 'Image' . $type;
		$ImageFun ($im);
		imagedestroy($im);
	}
}   
   
 // File end