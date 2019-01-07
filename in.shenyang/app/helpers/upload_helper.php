<?php
/**
 * 上传用的函数
 * Create by 2012-12-4
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
  
/**
 * 添加水印
 * Create by 2012-12-4
 * @author liuweijava
 * @param string $file 底图的绝对路径
 * @param string $water 水印图片的绝对路径
 * @param int $location 水印添加位置，
 * 						1=底图左上角；
 * 						2=底图顶部正中；
 * 						3=底图右上角；
 * 						4=底图中部左边；
 * 						5=底图正中，忽略dis_x和dis_y两个参数；
 * 						6=底图中部右边；
 * 						7=底图左下角；
 * 						8=底图底部正中；
 * 						9=底图右下角
 * @param boolean $add_string 添加的水印是否是文字
 */
function watermark($file, $water, $location = 9, $add_string = false){
	global $CI;
	//水印配置参数
	$conf = $CI->config->item('water_cfg');
	//获取图片对象
	list($src_w, $src_h, $src_type) = getimagesize($file);
	$src_type = image_type_to_mime_type($src_type);
	//GIF动画不加水印
	if($src_type !== 'image/gif' && !_check_animation($file)){
		//底图对象
		switch($src_type){
			//GIF图片
			case 'image/gif':$src = imagecreatefromgif($file);break;
			//JPG图片
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$src = imagecreatefromjpeg($file);
				break;
			//PNG图片
			case 'image/png':
			case 'image/x-png':
				$src = imagecreatefrompng($file);
				break;
			default://图片格式不合法
				$src = false;
				break;
		}
		if(!$src)
			return array('result_code'=>9, 'result_msg'=>'图片格式不合法');
		//颜色
		$d = imagecreatetruecolor($src_w, $src_h);
		$black = imagecolorallocate($d, 0, 0, 0);
		imagedestroy($d);
		//水印图对象
		if(!$add_string){
			list($water_w, $water_h, $water_type) = getimagesize($water);
			$water_type = image_type_to_mime_type($water_type);
			switch($water_type){
				//GIF图片
				case 'image/gif':$wtr = imagecreatefromgif($water);break;
				//JPG图片
				case 'image/pjpeg':
				case 'image/jpeg':
				case 'image/jpg':
					$wtr = imagecreatefromjpeg($water);
					break;
				//PNG图片
				case 'image/png':
				case 'image/x-png':
					$wtr = imagecreatefrompng($water);
					break;
				default:
					$wtr = false;
					break;
			}
		}else{
			$wtr = 'string';
			$water_w = strlen($water)*10;
			$water_h = 15;
		}
		if(!$wtr)
			return array(array('result_code'=>10, 'result_msg'=>'水印格式不合法'));
		//确定水印位置
		$dis_x = $dis_y = 0;
		if($location == 1){
			$dis_x = 0+$conf['dis_x'];
			$dis_y = 0+$conf['dis_y'];
		}elseif($location == 2){
			$dis_x = floor(((float)$src_w - $water_w) / 2);
			$dis_y = 0+$conf['dis_y'];
		}elseif($location == 3){
			$dis_x = $src_w - $water_w - $conf['dis_x'];
			$dis_y = 0+$conf['dis_y'];
		}elseif($location == 4){
			$dis_x = 0+$conf['dis_x'];
			$dis_y = floor(((float)$src_h - $water_h) / 2);
		}elseif($location == 5){
			$dis_x = floor(((float)$src_w - $water_w) / 2);
			$dis_y = floor(((float)$src_h - $water_h) / 2);
		}elseif($location == 6){
			$dis_x = $src_w - $water_w - $conf['dis_x'];
			$dis_y = floor(((float)$src_h - $water_h) / 2);
		}elseif($location == 7){
			$dis_x = 0+$conf['dis_x'];
			$dis_y = $src_h - $water_h - $conf['dis_y'];
		}elseif($location == 8){
			$dis_x = floor(((float)$src_w - $water_w) / 2);
			$dis_y = $src_h - $water_h - $conf['dis_y'];
		}else{
			$dis_x = $src_w - $water_w - $conf['dis_x'];
			$dis_y = $src_h - $water_h - $conf['dis_y'];
		}
		//加水印
		if($wtr === 'string'){
			imagestring($src, 12, $dis_x, $dis_y, $water, $black);
		}else{
			imagecopy($src, $wtr, $dis_x, $dis_y, 0, 0, $water_w, $water_h);
			imagedestroy($wtr);
		}
		//覆盖原图
		switch($src_type){
			//GIF
			case 'image/gif':
				imagegif($src, $file);
				break;
			//JPG
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				imagejpeg($src, $file, 100);
				break;
			//PNG
			case 'image/png':
			case 'image/x-png':
				imagepng($src, $file);
				break;
		}
		//清理
		imagedestroy($src);
		unset($black, $conf);
	}
	return array('result_code'=>0, 'result_msg'=>'Success');
}

/**
 * 把本地文件转移到
 * Create by 2012-12-5
 * @author liuweijava
 * @param string $file 本地文件的绝对路径
 */
function move_file_to_remote($file){
	global $CI;
	$conf = $CI->config->item('img_remote');
	$f_name = pathinfo($file, PATHINFO_BASENAME);
	$f_suffix = pathinfo($file, PATHINFO_EXTENSION);
	//解析获得文件保存路径
	list($f_yearmonth, $f_day, $f_time, $f_cat) = explode('_', str_replace('.'.$f_suffix, '', $f_name));
	$farr = array($f_yearmonth, $f_day);
	$folder = implode('/', $farr);
	//远程目录
	$rmt_folder = 'picture'.$f_cat;
	//远程文件的绝对路径
	$rmt_abs_path = $conf['root'] . $rmt_folder . '/' . $folder;
	$rmt_abs_file = $rmt_abs_path . '/' . $f_name;
	//检查并创建目录
	$p = $conf['root'] . $rmt_folder . '/';
	foreach($farr as $k=>$f){
		$p .= $f.'/';
		if(!file_exists($p) && !is_dir($p)){
			mkdir($p);//创建目录
			chmod($p, 0755);//分配写权限
		}
	}
	//移动文件
	rename($file, $rmt_abs_file);
	//清理垃圾
	@unlink($file);
	unset($file, $f_suffix, $rmt_folder, $farr, $p, $rmt_abs_path);
	//返回访问路径
	$domains = $conf['domain'];
	return $domains[$f_cat] . '/' . $folder . '/' . $f_name;
}

/**
 * FTP上传 
 * Create by 2012-12-4
 * @author liuweijava
 * @param string $file_name 本地文件名称，需要通过这个文件名分析上传到哪个图片服务器及上传的路径
 */
function ftp_upload($file_name){
	global $CI;
	$ftp_servs = $CI->config->item('ftp_serv');
	$CI->load->library('ftp');
	$c = $CI->config->item('image_cfg');
	$folder = $c['upload_path'];
	//解析FTP路径
	$file = explode('.', $file_name);
	list($p_ym, $p_day, $p_time, $ftpno) = explode('_', $file[0]);
	$parr = array($p_ym, $p_day);
	//获得FTP服务器配置
	$serv = $ftp_servs[$ftpno];
	//创建FTP连接
	$this->ftp->connect($serv);
	//创建目录
	$path = '/';
	foreach($parr as $k=>$p){
		$path .= $p . '/';
		$this->ftp->mkdir($path);
		//设置目录写权限
		$this->ftp->chmod($path, DIR_WRITE_MODE);
	}
	//上传图片
	$real_path = $folder . $file_name;
	$this->ftp->upload($real_path, $path . $file_name, 'auto', 0755);
	//关闭FTP连接
	$this->ftp->close();
	//删除本地文件
	unlink($real_path);
}

/**
 * 检查GIF是否是动画
 * Create by 2012-12-5
 * @author liuweijava
 * @param string $file GIF图片的绝对路径
 * @return boolean
 */
function _check_animation($file){
	$f = fopen($file, 'rb');
	$char = fread($f, 1024);
	fclose($f);
	return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/", $char)  ? true : false;
}
   
 // File end