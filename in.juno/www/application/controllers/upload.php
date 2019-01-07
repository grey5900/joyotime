<?php
/**
 * Create by 2012-12-12
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Upload extends Controller{
	
	var $_cfg;
	
	public function __construct(){
		parent::__construct();
		//参数
		$this->config->load('config_image');
		$this->_cfg = $this->config->item('image_cfg');
		//library
		$this->load->library('image_factory');
	}
	
	/**
	 * 上传POST图片
	 * Create by 2012-12-26
	 * @author liuweijava
	 */
	public function upload_image(){
		$upload_path = $this->_cfg['upload_path'];
		$uid = intval($this->post("uid"));
		list($usec, $sec) = explode(" ", microtime());
		$timer = (float)$usec + (float)$sec;
		$customer = $uid."_".$timer."_".rand(1,9999999);
		if(!empty($_FILES)){
			if($_FILES['Filedata']['error']){
				$data['code'] = -4;
				$data['msg'] = '图片上传失败，可能是图片损坏或者图片过大！';
				$this->echo_json($data);
				exit;
			}
			//上传的缓存
			$cache_file = $_FILES['Filedata']['tmp_name'];
			//本地文件路径
			$file = $_FILES['Filedata']['name'];
			//检查文件格式是否合法
			$file_arr = pathinfo($file);
			$filename = $file_arr['basename'];
			$suffix   = $file_arr['extension'];		
			$local_file = $upload_path .$customer. ".".$suffix;
			list($img_w,$img_h) = @getimagesize($cache_file);
			$max_img_w = $this->_cfg['max_width'];
			$max_img_h = $this->_cfg['max_height'];
			if($img_w > $max_img_w || $img_h > $max_img_h ){
				$data['code'] = -3;
				$data['msg'] = '图片尺寸过大，最大尺寸'.$max_img_w.'x'.$max_img_h;
				$this->echo_json($data);
				exit;
			}
			if(!in_array(strtolower($suffix), $this->_cfg['allowed_image_ext'])){
				$data['code'] = -1;
				$data['msg'] = '非法的文件类型'.$suffix;
			}
			else{
				//检查文件大小
				$max_size = $this->_cfg['max_file']*1024*1024;
				if($_FILES['Filedata']['size']>$max_size){
					$data['code'] = -2;
					$data['msg'] = '文件过大，最大'.$this->_cfg['max_file'].'M';
				}
				else{
					//保存到本地
					move_uploaded_file($cache_file, $local_file);
					$data['code'] = 1;
					$data['msg'] = '/images/'.basename($local_file);
				}
			}
		}else{
			$data['code'] = 0;
			$data['msg'] = "请上传图片";
		}
		$this->echo_json($data);
	}
	
	/**
	 * 上传头像
	 * Create by 2012-12-12
	 * @author liuweijava
	 */
	public function avatar(){
		//图片参数
		$this->config->load('config_image');
		$this->_cfg = $this->config->item('image_cfg');
		$upload_path = $this->_cfg['upload_path'];
		$uid = intval($this->post("uid"));
		list($usec, $sec) = explode(" ", microtime());
		$timer = (float)$usec + (float)$sec;
		$customer = $uid."_".$timer."_".rand(1,9999999);
		if(!empty($_FILES)){
			if($_FILES['Filedata']['error']){
				$data['code'] = -4;
				$data['msg'] = '图片上传失败，可能是图片损坏或者图片过大！';
				$this->echo_json($data);
				exit;
			}
			//获取缓存文件
			$cache_file = $_FILES['Filedata']['tmp_name'];
			//file type
			$file = $_FILES['Filedata']['name'];
			$file_arr = pathinfo($file);
			$filename = $file_arr['basename'];
			$suffix   = $file_arr['extension'];		
			$local_file = $upload_path .$customer. ".".$suffix;
			
			list($img_w,$img_h) = @getimagesize($cache_file);
			$max_img_w = $this->_cfg['max_width'];
			$max_img_h = $this->_cfg['max_height'];
			if($img_w > $max_img_w || $img_h > $max_img_h ){
				$data['code'] = -3;
				$data['msg'] = '图片尺寸过大，最大尺寸'.$max_img_w.'x'.$max_img_h;
				$this->echo_json($data);
				exit;
			}
			//检查文件类型，合法的才保存到本地
			if(!in_array(strtolower($suffix), $this->_cfg['allowed_image_ext'])){
				$data['code'] = -1;
				$data['msg'] = '非法的文件类型'.$suffix;
				$this->echo_json($data);exit;
			}
			//检查文件大小
			$max_size = $this->_cfg['max_file']*1024*1024*2;
			if($_FILES['Filedata']['size']>$max_size){
				$data['code'] = -2;
				$data['msg'] = '文件过大，最大'.$this->_cfg['max_file'].'M';
				$this->echo_json($data);exit;
			}
			if(in_array(strtolower($suffix), $this->_cfg['allowed_image_ext'])){
				//保存到本地
				$flag = move_uploaded_file($cache_file, $local_file);
				//压缩并裁切到360*360
				list($w, $h, $t) = getimagesize($local_file);
				if($w > 360 || $h > 360){
					//缩放比例
					$scale = $w < $h ? 360 / $w : 360 / $h;
					//缩略
					$this->image_factory->resizeImage($local_file, $w, $h, $scale);
					//裁切
					list($w, $h, $t) = getimagesize($local_file);
					if($w == 360){
						$dx = 0;
						$dy = ($h - 360) / 2;
					}else{
						$dx = ($w - 360) / 2;
						$dy = 0;
					}
					$res = $this->image_factory->resizeThumbnailImage($local_file, $local_file, 360, 360, $dx, $dy, 1);
				}
				else{
					if($w < 360 || $h<360){
						//小于360的，生成一个在中间的图
						$start_x = $start_y = 0;
						if($w<360 && $h<360){
							$start_x = ceil((360-$w)/2) ;
							$start_y = ceil((360-$h)/2) ;
						}
						else if($w<360){
							$start_x = ceil((360-$w)/2) ;
						}else if($h<360){
							$start_y = ceil((360-$h)/2) ;
						}
						//计算scale，让图片w * scale和  h * scale都 大于等于360
						$scale = 360/$w < 360 / $h ? 360 / $h  : 360/$w;
					$res = $this->image_factory->resizeThumbnailImage($local_file, $local_file, 360, 360, 0, 0, 1,$start_x,$start_y,$w,$h);
					}
				}
				//var_dump($local_file);
				if($res == "Image not supportted !"){
					$data['code'] = -1;
					$data['msg'] = "不支持的文件类型";
				}else{
					$data['code'] = 1;
					$data['msg'] = "/images/".basename($local_file);
					
				}
			}else{
				$data['code'] = -1;
				$data['msg'] = "不支持的文件类型";
				//die('Invalid file type.');
			}
		}else{//没有上传文件
			$data['code'] = 0;
			$data['msg'] = "请上传图片";
			//die('Invalid file.');
		}
		$this->echo_json($data);
	}
	
}   
   
 // File end