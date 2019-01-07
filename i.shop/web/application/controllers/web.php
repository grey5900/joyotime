<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 渠道商index类
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-12
 */
 
class Web extends MY_Controller {
	
	function index(){
		$this->is_login();
		$this->assign('active', 'index');
		$msgs = $reads = $places = array();
		
		//获取商家平台未读的消息
		$brandId = $this->auth['brand_id'];
		$query = $this->db->where(array('brandId'=>$brandId, 'status'=>0))->order_by('createDate', 'desc')->get('BrandMessage')->result_array();
		foreach($query as $row){
			$msgs[$row['id']] = $row;
		}
		//已读的最近10条
		$query = $this->db->where(array('brandId'=>$brandId, 'status'=>10))->order_by('createDate', 'desc')->limit(10)->get('BrandMessage')->result_array();
		foreach($query as $row){
			$reads[$row['id']] = $row;
		}
		//未读设置为已读
		$this->db->where(array('brandId'=>$brandId, 'status'=>0))->set('status', 10, false)->update('BrandMessage');
		//获取商家店铺列表
		$query = $this->db->where(array('brandId'=>$brandId))->order_by('createDate', 'desc')->get('Place')->result_array();
		foreach($query as $row){
			$places[$row['id']] = $row['placename'];
		}
		
		//统计报表配置
		$this->config->load('chart_config');
		$charts = $this->config->item('charts');
		
		$this->assign(compact('msgs', 'places', 'reads', 'charts'));
		$this->display('index');
	}
	    
    /**
     * 登录
     */
    function login() {
        if($this->is_post()) {
            // 判断账号
            $username = trim($this->post('username'));
            if(empty($username)) {
                $this->echo_json(array('code'=>1,'msg'=>'请输入登陆账号'));
            }
            
            // 判断密码
            $password = $this->post('password');
            if(empty($password)) {
                $this->echo_json(array('code'=>1,'msg'=>'请输入登陆密码'));
            }
            
            // 获取IP地址
            $ip_address = ip2long($this->input->ip_address());
            // 禁言时间
            $banned_time = now(0, $this->timestamp - $this->config->item('login_error_time'));

            // 删除已经过了禁止登录时间的记录
            $this->db->delete('FailedLogin', array('lastDate < ' => $banned_time));
            
            // 获取禁止记录
            $failed_login = $this->db->get_where('FailedLogin', array('ip' => $ip_address))->row_array();
            if($failed_login && ($failed_login['loginCount'] > $this->config->item('login_error_num'))) {
                $this->echo_json(array('code'=>1,'msg'=>'登录错误超过5次，请' . (int)($this->config->item('login_error_time')/60) . '分钟之后再试'));
            }
            
            // 获取用户数据
            $user = $this->db->get_where('User', array('username' => $username))->row_array();
            $pass_valid = $user['password'] != strtoupper(md5($password));
            if(empty($user) || $pass_valid) {
                if($pass_valid) {
                    // 密码错误，那么更新数据库的登录次数
                    if($failed_login) {
                        $failed_login['loginCount']++;
                    } else {
                        $failed_login = array('ip' => $ip_address, 'loginCount' => 1, 'lastDate' => now());
                    }
                    $this->db->where(array('ip' => $ip_address))->replace('FailedLogin', $failed_login);
                }
               // $this->echo_json(array('code'=>0, 'msg'=>'账号或密码错误'));
								$this->echo_json((array('code'=>1,'msg'=>$this->lang->line('login_fail'))));
            }
            
            if($user['status'] > 0) {
                $this->echo_json(array('code'=>1, 'msg'=>'账号已失效，请联系管理员'));
            }
            
            //非店长不能使用商家平台
            if(intval($user['isVerify']) != 1)
            	$this->echo_json(array('code'=>1, 'msg'=>'非商家平台用户，不能使用商家平台'));
            //品牌失效的也不能使用
            $brand = $this->db->where('lordId', $user['id'])->get('Brand')->first_row('array');
            if(empty($brand) || $brand['status'] != 0)
            	$this->echo_json(array('code'=>1, 'msg'=>'非认证商家，不能使用商家平台'));
            
            // 更新用户表数据
    //        $this->db->update('ChannelUser', array('lastDate'=>$this->timestamp, 'lastIp'=>$ip_address), array('id'=>$user['id']));
            
            // 登录成功那么清楚错误记录
            $this->db->delete('FailedLogin', array('ip' => $ip_address));
            
            // 登录成功，记录用户的cookie信息            
            $auto_login = $this->post('auto_login');
            // cookie中只保存id username brand_id brand
            // 去获取渠道信息
      //      $channel = $this->db->get_where('ChannelInfo', array('id' => $user['channelId']))->row_array();
      //      $channel['startDate'] = substr($channel['startDate'], 0, 10);
            $cookie_value = authcode("{$user['id']}\t{$user['username']}\t{$brand['id']}\t{$brand['name']}", 'ENCODE', $this->config->item('authcode_key'));
            
            $cookie_life = $auto_login?31536000:0;
            dsetcookie($this->config->item('cookie_name'), $cookie_value, $cookie_life);
            
            $this->echo_json(array('code'=>0, 'msg'=>'感谢您使用IN成都商家平台', 'refer'=>site_url()));
        }
        
        $this->display('login');
    }

    /**
     * 退出登录
     */
    function logout() {
        dsetcookie($this->config->item('cookie_name'), NULL, -1);
        redirect('/login');
    }
    
    /**
     * 修改密码
     */
    function modify_pass() {
        if($this->is_post()) {
            $oldpwd = $this->post('oldpwd');
            $newpwd = $this->post('newpwd');
            $renew = $this->post('renew');
            
            if($newpwd !== $renew) {
                $this->echo_json(array('code'=>1,'msg' => '请输入相同的密码'));
            }
            if(strlen($newpwd) < 2 || strlen($newpwd) > 15){
            	$this->echo_json(array('code'=>1,'msg'=>'合法到密码长度是2-15个字符'));
            }
            
            $user = $this->db->get_where('User', array('username' => $this->auth['name']))->row_array();
            if(strtoupper(md5($oldpwd)) != $user['password']) {
                $this->echo_json(array('code'=>1, 'msg' => '输入的旧密码不正确'));
            }
            
            // 更新用户密码，退出登录，
            $this->db->update('User', array('password' => strtoupper(md5($newpwd))), array('username' => $this->auth['name']));
            
            $this->echo_json(array('code' => 0, 'refer' => '/logout', 'msg' => '修改密码成功，请重新登录'));
        }
        $this->assign('active', '');
        $this->display('modify_pwd');
    }
    
    /**
     * 上传扩展信息图片，
     * Create by 2012-10-16
     * @author liuw
     * @param int $resize，是否缩放裁切，默认为1，表示要缩放裁切，0表示不做处理
     * @param int $style，封面样式，默认为2
     */
    public function upload_profile($resize=1, $style=3){
    	$upload = $this->post('uploadFile');
    	empty($upload) && $upload = 'Filedata';
    	$cfg = $this->config->item('image_cfg');
    	$upload_path = $cfg['upload_path'];
    	if(!empty($_FILES)){
			$tempFile = $_FILES[$upload]['tmp_name'];
			$targetPath = $upload_path;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES[$upload]['name'];
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES[$upload]['name']);
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				move_uploaded_file($tempFile,$targetFile);
    			@chmod(0777, $targetFile);
    			if($resize){
    				//裁切
    				$cfg_name = $style - 1 == 1? 'cover_img_2' : 'cover_img';
    				$size = $this->config->item($cfg_name);
    		//		file_put_contents('test.txt', current_url()."\n".$resize."\n".$style."\n".$cfg_name."\n".print_r($size, true));
					$this->load->library('upload_image');
					list($w, $h, $t, $at) = getimagesize($targetFile);
					//缩放图片，宽缩到规定尺寸
					if(in_array($style, array(1, 3)))
						$scale = $w < $h ? $size['w'] / $w : $size['h'] / $h;
					else
						$scale = $size['w'] / $w;
					$this->upload_image->resizeImage($targetFile, $w, $h, $scale);
					//裁切图片
					list($w, $h, $t, $at) = getimagesize($targetFile);
					//计算裁切的左上角坐标
					if(in_array($style, array(1, 3))){
						if($w == $size['w']){
							$start_x = 0;
							$start_y = ($h - $size['h']) / 2;
							$scale = $size['h'] / $h;
						}else{
							$start_y = 0;
							$start_x = ($w - $size['w']) / 2;
							$scale = $size['w'] / $w;
						}
					}else{
						$w < $size['w'] && $size['w'] = $w;
						$h < $size['h'] && $size['h'] = $h;
					}
					$targetFile = $this->upload_image->resizeThumbnailImage($targetFile, $targetFile, $size['w'], $size['h'], $start_x, $start_y, 1);
				}
				$this->echo_json(array('code'=>0, 'src'=>$_FILES[$upload]['name']));
    		}else 
    			$this->echo_json(array('code'=>1, 'msg'=>'请按顺序依次上传图片，谢谢'));
    	}else 
    		$this->echo_json(array('code'=>1, 'msg'=>'非法请求'));
    }
    
    function upload_to_pic(){
    	if($this->is_post()){
	    	$cfg = $this->config->item('image_cfg');
	    	$upload_path = $cfg['upload_path'];
			$path = rtrim($upload_path,'/') . '/';
    		$name = $this->post('name');
    		$file = $path . $name;
    		$param = array(
    			'api'		 => $this->lang->line('api_upload_image'),
				'uid'		 => $this->auth['id'],
				'has_return' =>	true,
				'attr'		 => array(
					'file'		 => '@'.$file,
					'file_type'  => 'common',
					'resolution' => 'odp'
				)
    		);
			$result = json_decode($this->call_api($param), true);
			@unlink($file);
			if(intval($result['result_code']) != 0)
				$this->echo_json(array('code'=>$result['result_code'], 'msg'=>$result['result_msg']));
			else
				$this->echo_json(array('code'=>0, 'view'=>image_url($result['file_name'], 'common', 'odp'), 'file_name'=>$result['file_name']));
    	}
    }
    
    /**
     * 上传照片到网站服务器
     * Create by 2012-6-12
     * @author liuw
     * @param int $type，0=优惠，1=会员卡
     */
    public function upload($type=0){
    	$cfg = $this->config->item('image_cfg');
    	$upload_path = $cfg['upload_path'];
    	if(!empty($_FILES)){
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$filesize = filesize($tempFile);
			$filesize > $cfg['image_size'] && $this->echo_json(array('code'=>2, 'msg'=>'图片太大了，请上传大小不超过2MB的图片'));
			$targetPath = $upload_path;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				move_uploaded_file($tempFile,$targetFile);
			//	@chmod(0777, $targetFile);
				//缩略图尺寸
				$t_scale = $type ? $this->config->item('card_bimg') : $this->config->item('prefer_img');
				$bl = (float)$t_scale['w'] / $t_scale['h'];
				$this->load->library('upload_image');
				//压缩原图，短边固定到360,长边等比例压缩
				list($w, $h, $t, $at) = getimagesize($targetFile);
			//	$up_bl = (float)$w / $h;
				$cfg = $t_scale;
			//	$ds_h = floor($w * ((float)$t_scale['h']/$t_scale['w']));
				if($w != $t_scale['w'] || ($w == $t_scale['w'] && $h != $t_scale['h'])){
					$this->echo_json(array('code'=>1, 'msg'=>'为了保证图片质量，请确定上传到图片尺寸等于'.$cfg['w'].'x'.$cfg['h']));
				}else{
					$scale = 640 / $w;
					$this->upload_image->resizeImage($targetFile, $w, $h, $scale);
					//裁切原图，生成360*360的原图
			/*		list($w, $h, $t, $at) = getimagesize($targetFile);
					if($w == 360){
						$s_w = 0;
						$s_h = ($h - 640) / 2;
						$scale = 640 / $h;
					}else{
						$s_w = ($w - 640) / 2;
						$s_h = 0;
						$scale = 640 / $w;
					}
					$this->upload_image->resizeThumbnailImage($targetFile, $targetFile, 640, 640, $s_w, $s_h, 1); */
					$this->echo_json(array('code'=>0, 'src'=>'/data/img/'.$_FILES['Filedata']['name'], 'file'=>$_FILES['Filedata']['name'], 'y2'=>$cfg['h'], 'x2'=>$cfg['w']));
				}
			} else {
				$this->echo_json(array('code'=>2, 'msg'=>'图片上传失败'));
			}
    	}else{
			$this->echo_json(array('code'=>2, 'msg'=>'请选择图片'));
    	}
    }
    
    function test(){
    	$this->display('test');
    }
}
