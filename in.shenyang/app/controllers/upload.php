<?php
/**
 * 上传文件 函数
 * Create by 2012-12-5
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Upload extends MY_Controller{
	
	/**
	 * 上传文件
	 * Create by 2012-12-5
	 * @author liuweijava
	 * @param arr $_FILES 上传的文件，为空将结束上传流程
	 * @param int $tw 缩略图宽度，为空不缩略
	 * @param int $th 缩略图高度，为空时等比例缩略
	 * @param string input FORM提交的INPUT名，为空默认为"file"
	 * @param string water 水印文件名。为空表示不加水印
	 * @param int water_location 水印位置，默认为9，详情见upload_helper->watermark的注释
	 */
	public function index($rtn = false, $image_key = ''){
		if($this->is_post()){
			empty($_FILES) && $this->echo_json(array('result_code'=>999, 'result_msg'=>'您没有上传任何图片'));
			$tw = $this->post('tw');
			empty($tw) && $tw = false;
			$th = $this->post('th');
			empty($th) && $th = false;
			$input = $this->post('input');
			empty($input) && $input = 'file';
			empty($_FILES[$input]) && $this->echo_json(array('result_code'=>999, 'result_msg'=>'您没有上传任何图片'));
			
			
			$cache_conf = $this->config->item('image_cfg');
			$up_file = $_FILES[$input];
			$file_type = pathinfo($up_file['name'], PATHINFO_EXTENSION);
			$allow_image = $cache_conf['allowed_image_ext'];
			if(!in_array(strtolower($file_type), $allow_image)) {
				$rtn_msg = array('result_code'=>1, 'result_msg'=>'图片格式不正确,服务器只接受JPG、PNG、GIF三种格式的图片');
				if($rtn) {
					return $rtn_msg;
				} else {
					$this->echo_json($rtn_msg);
				}
			}
			
			//确定文件名
			if($image_key === '') {
    			$random = floor(rand(0, 9) / 3);
    			$f_cat = $random > 0 ? ($random > 2 ? 2 : $random) : 3;
			} else {
			    $f_cat = $image_key;
			}
			//文件名
			$now = time()+8*3600;
			$f_preffix = gmdate('Ym_d_His', $now).substr($now.'', -3).(0+microtime());
			$filename = $f_preffix . '_' . $f_cat . '.' . $file_type;
			//本地路径
			$local = $cache_conf['upload_path'].$filename;
			//上传到本地
			move_uploaded_file($up_file['tmp_name'], $local);
			chmod($local, 0755);
			
			//需要缩略的
			if($tw){
				$this->load->library('upload_image');
				list($w, $h, $t) = getimagesize($local);
				if($w > $tw){//原图宽度大于缩略宽度才缩略
					$scale = 1;
					//缩放比例
					if($th && $th < $tw){
						$scale = $th / $h;
					}else{
						$scale = $tw / $w;
					}
					//先缩小
					$local = $this->upload_image->resizeImage($local, $w, $h, $scale);
					//再裁切
					list($w, $h, $t) = getimagesize($local);
					if($th && $h > $th && $w > $tw){
						$local = $this->upload_image->resizeThumbnailImage($local, $local, $tw, $th, 0, 0, 1);
					}
				}
			}
			$this->load->helper('upload');
			//打水印
			$water = $this->post('water');
			$water_location = $this->post('location');
			empty($water_location) && $water_location = 9;
			if(!empty($water)){
				//水印的绝对路径
				$wconf = $this->config->item('water_cfg');
				$add_string = file_exists($wconf['root'] . $water) ? false : true;
				!$add_string && $water = $wconf['root'] . $water;
				$result = watermark($local, $water, intval($water_location), $add_string);
				if($result['result_code'] > 0)
					$this->echo_json($result);
			}			
			//移动到远程目录
			$remote = move_file_to_remote($local);
			$rtn_msg = array('result_code'=>0, 'msg'=>'图片上传成功', 'file_name'=>$remote);
			if($rtn) {
				return $rtn_msg;
			} else {
				$this->echo_json($rtn_msg);
			}
		}
	}
	
	/**
	 * 转换一次上传，因为本来写的那个不能满足新闻那里
	 */
	function up() {
		$_POST['input'] = 'filedata';
		$file = $_FILES['filedata'];
		
		$result = $this->index(true);
		
		if ($result['result_code']) {
			// 上传失败
			die("{'err':'1','msg':{'url':'','localname':'{$file['name']}','id':'1'}}");
		} else {
			// 上传成功
			die("{'err':'','msg':{'url':'{$result['file_name']}','localname':'{$file['name']}','id':'1'}}");
		}
	}
	
	/**
	 * 换了ueditor，所以又写了个转换传图的
	 */
	function up2() {
		
	    $_POST['input'] = 'upfile';
	    $_POST['water'] = 'water.png';
	    $file = $_FILES['upfile'];

	    $result = $this->index(true);
	    
	    $title = $this->post('pictitle');
	    	    
	    die("{'url':'" . $result['file_name'] . "','title':'" . $title . "','original':'" . $file['name'] . "','state':'" . ($result['result_code']?'':'SUCCESS') . "'}");
	
		
	}
	
	/**
	 * 换了ckeditor，所以又写了个转换传图的
	 */
	function up3() {
	    $_POST['input'] = 'upload';
	    $file = $_FILES['upload'];

	    $result = $this->index(true);
	    
	    //$title = $this->post('pictitle');
	    $callback = $this->get('CKEditorFuncNum');	    
	    //die("{'url':'" . $result['file_name'] . "','title':'" . $title . "','original':'" . $file['name'] . "','state':'" . ($result['result_code']?'':'SUCCESS') . "'}");
		die("<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$callback.",'".$result['file_name']."','')</script>");
	}

	public function upload_image(){
		//$this->config->load('config_image');
		$_cfg = $this->config->item('image_cfg');
		$upload_path = $_cfg['upload_path'];
		//var_dump($_FILES);exit;
		if(!empty($_FILES)){
			//上传的缓存
			$cache_file = $_FILES['file']['tmp_name'];
			//本地文件路径
			$file = $_FILES['file']['name'];
			$local_file = $upload_path . $file;

			//检查文件格式是否合法
// 			list($name, $suffix) = explode('.', $file);
			$suffix = substr($file, strrpos($file, '.') + 1);
			
			if(!in_array(strtolower($suffix), $_cfg['allowed_image_ext'])){
				$data['result_code'] = -1;
				$data['result_msg'] = '非法的文件类型';
				die(json_encode($data));
			}else{
				if($_FILES['file']['error']){
					$data['result_code'] = -3;
					$data['result_msg'] = '上传图片失败';
					die(json_encode($data));
				}
				$max_size = $_cfg['max_file']*1024*1024;
				if($_FILES['file']['size']>$max_size){
					$data['result_code'] = -2;
					$data['result_msg'] = '文件过大，最大'.$this->_cfg['max_file'].'M';
					die(json_encode($data));
				}
				else{
					//保存到本地
					move_uploaded_file($cache_file, $local_file);
					$data['result_code'] = 0;
					$data['result_msg'] = "操作成功";
					$data['file_name'] = "http://".$_SERVER['HTTP_HOST'] . $_cfg['upload_view'] .basename($local_file);//$local_file;
					$data['real_name'] = $local_file;
					//die($local_file);
					die(json_encode($data));
				}
			}
		}else{
			die('上传图片');
		}
	}
}
 // File end