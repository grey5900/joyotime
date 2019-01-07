<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 一些公用及主要的Controller
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-8
 */

class Main extends MY_Controller {

    /**
     * 主要页面
     * 进行一些缓存的初始化等工作
     */
    function index() {
        // 读取用户的菜单
        $menus = get_data('menu');
        $this->load->helper('menu');
        $roles = get_data('role');
        $rights = array();
        $newsRights = array();
        if (in_array($this->config->item('superadmin'), $this->auth['role'])) {
            // 超级管理员
            $inc_rights = get_data('rights_id');
            $rights = array_keys($inc_rights);
        } else {
            foreach ($this->auth['role'] as $role_id) {
                $role = $roles[$role_id];
                $rights = array_merge($rights, array_values($role['rights']));
                $newsRights = array_merge($newsRights, array_values($role['newsRights']));
            }
        }
        $this->assign(array(
                'auth_name' => $this->auth['name'],
                'menu_str' => build_menu($menus, $rights),
                'upload_uri' => $this->config->item('upload_image_api'),
                'upload_uri_v3' => $this->config->item('v3_upload_image_api')
        ));

        $this->display('index');
    }

    /**
     * 退出
     */
    function logout() {
        // 清除所有SESSION
        // $this->session->sess_destroy();
        $_SESSION[$this->config->item('sess_auth')] = array();
        // 跳到登录页面
        forward(site_url(array(
                'main',
                'login'
        )));
    }

    /**
     * 用户登录
     */
    function login() {
        if ('POST' == $this->server('REQUEST_METHOD')) {
            // 提交登陆
            // 先判断验证码
            $this->load->library('session');
            $this->session->sess_expire_on_close = TRUE;
            if ($this->post('valicode') != $this->session->userdata($this->config->item('sess_captcha'))) {
                // 验证码错误
                $this->error($this->lang->line('invalid_code'));
            }
            // 清空验证码session
            $this->session->unset_userdata($this->config->item('sess_captcha'));
            // 判断账号和密码
            $username = trim($this->post('username'));
            $password = $this->post('password');
            if (empty($username) || empty($password)) {
                // 错误的登录
                $this->error($this->lang->line('login_error'));
            }

            // 数据库取数据
            $admin_row = $this->db->where('name', $username)->get_where('MorrisAdmin')->row_array();
            if (empty($admin_row) || md5($password) != $admin_row['password']) {
                // 如果是空
                $this->error($this->lang->line('login_error'));
            } elseif (!$admin_row['state']) {
                $this->error($this->lang->line('account_has_not_start'));
            }

            // 保存登录用户的SESSION
            // id,name,truename,description,rights,role
            $this->load->helper('array');
            $sess_auth = elements(array(
                    'id',
                    'name',
                    'truename',
                    'description'
            ), $admin_row);
            //$sess_auth['vest'] = unserialize($admin_row['vest']);
            $sess_auth['role'] = explode(',', $admin_row['role']);
            // if (in_array($this->config->item('superadmin'), $sess_auth['role'])) {
            // // 如果用户组权限有超级管理员权限
            // $inc_rights = get_data('rights_id');
            // $rights = array_keys($inc_rights);
            // } else {
            // $role = get_data('role');
            // $rights = array();
            // foreach ($sess_auth['role'] as $roleid) {
            // // 获取该用户的所有权限
            // foreach ($role[$roleid]['rights'] as $rights_id) {
            // $rights[] = $rights_id;
            // }
            // }
            // }
            // $sess_auth['rights'] = $rights;
            // $this->session->set_userdata($this->config->item('sess_auth'), $sess_auth);
            // 用SESSION来保存
            $_SESSION[$this->config->item('sess_auth')] = $sess_auth;

            // 更新用户的最后登录时间
            $this->db->where('id', $sess_auth['id'])->update('MorrisAdmin', array('lasttime' => now()));

            // 返回登录成功
            $this->success($this->lang->line('login_success'), '', '/', 'forward');
        } else {
            $this->display('login');
        }
    }

    /**
     * 验证码图片
     */
    function captcha() {
        $this->load->helper('valicode');
        $words = strtolower(random_string('alnum', 4));
        $this->session->set_userdata($this->config->item('sess_captcha'), $words);
        create_valicode($words);
    }

    /**
     * 修改密码
     * Create by 2012-3-13
     * @author liuw
     * @param int $id,用户id
     */
    function modify_pass() {
        $callbackType = 'forward';
        $navTabId = '';
        if (!isset($this->auth) || empty($this->auth)) {
            $message = $this->lang->line('no_login');
            $forwardurl = site_url(array(
                    'main',
                    'login'
            ));
            $this->error($message, $navTabId, $forwardurl, $callbackType);
        } else {
            if ('POST' === $this->server('REQUEST_METHOD')) {
                $message = $this->lang->line('modify_pass_success');
                $forwardurl = site_url(array(
                        'main',
                        'logout'
                ));

                $id = $this->post('id');
                $oldpwd = $this->post('oldpwd');
                $newpwd = $this->post('newpwd');
                //检查旧密码是否正确
                $acc = $this->db->where('id', $id)->get('MorrisAdmin')->first_row('array');
                if (!isset($acc) || empty($acc)) {
                    $message = $this->lang->line('account_has_not_here');
                    $forwardurl = site_url(array(
                            'main',
                            'logout'
                    ));
                    $this->error($message, $navTabId, $forwardurl, $callbackType);
                } else {
                    if (strtolower(md5($oldpwd)) != $acc['password']) {
                        $message = $this->lang->line('modify_old_pwd_error');
                        $forwardurl = site_url(array(
                                'main',
                                'index'
                        ));
                        $this->error($message, $navTabId, $forwardurl, $callbackType);
                    } else {
                        $edit = array('password' => strtolower(md5($newpwd)));
                        $this->db->trans_strict(FALSE);
                        $this->db->trans_begin();
                        $this->db->where('id', $id);
                        $this->db->update('MorrisAdmin', $edit);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $message = $this->lang->line('do_error');
                            $forwardurl = site_url(array(
                                    'main',
                                    'index'
                            ));
                            $this->error($message, $navTabId, $forwardurl, $callbackType);
                        } else {
                            $this->db->trans_commit();
                            $this->success($message, $navTabId, $forwardurl, $callbackType);
                        }
                    }
                }
            }
            $this->assign('id', $this->auth['id']);
            $this->display('modify_pass');
        }
    }

    /**
     * 系统设定
     */
    function app_setting() {
        if ('POST' === $this->server('REQUEST_METHOD')) {
            // 提交
            $type = $this->post('type');
            $post = $this->post(NULL);
            unset($post['type']);
            $this->db->replace('AppSetting', array(
                    'skey' => $type,
                    'svalue' => serialize($post)
            ));

            get_data($type . '_setting', true);

            $this->success('系统设定提交成功', build_rel(array(
                    'main',
                    'app_setting'
            )), $this->_uri);
        }

        // 获取基础配置
        $common = get_data('common_setting');

        // 获取图片配置
        $image = get_data('image_setting');

        $this->assign(compact('common', 'image'));
        $this->display('app_setting');
    }

    /**
     * 得到图片的url地址
     */
    function get_image_url() {
        $image_name = $this->get('file_name');

        if (strpos($image_name, 'http://') === 0) {
            die(json_encode(array(
                    'type' => 'url',
                    'image_name' => $image_name,
                    'image' => $image_name,
                    'source_image' => $image_name
            )));
        }

        $file_type = $this->get('file_type');
        $resolution = $this->get('resolution');

        $image = image_url($image_name, $file_type, $resolution);
        $source_image = $image;

        echo json_encode(compact('image_name', 'image', 'source_image'));
    }

    /**
     * 空白
     */
    function none() {

    }

    /**
     * 测试方法
     */
    function test() {
        $rtn = request_api('/post/list_following', 'GET', array(), array('uid' => 1));
        
        var_dump($rtn);
    }

    /**
     * 地点的扩展信息
     */
    function place($id = 0, $mid = 0) {
        // 地点ID号
        $id = intval($id);
        $mid = intval($mid);

        ($id <= 0) && die('');

        // 地点信息
        $place = $this->db->get_where('Place', array('id' => $id))->row_array();
        $this->assign('title', $place['placename']);

        $place || die('');
        
        if(empty($mid)) {
            // 获取地点的第一个模型，兼容之前的版本
            $row = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceOwnModule', array('placeId' => $id))->row_array();
            $mid = $row['placeModuleId'];
        }
        
        if($mid > 0) {
            // 去获取碎片
            $block = $this->db->get_where('PlaceOwnSpecialProperty', array('placeId' => $id, 'moduleId' => $mid))->row_array();
            
            $block['title'] && $this->assign('title', $block['title']);
            
            // 去获取模型字段信息
            $rows = $this->db->get_where('PlaceModuleField', array('moduleId' => $mid))->result_array();
            $fields = array();
            foreach($rows as $row) {
                $fields[$row['fieldId']] = $row;
            }
            unset($row, $rows);
            
            // 地点扩展信息
            $ext_info = $this->db->get_where('PlaceModuleData', array(
                    'placeId' => $id,
                    'isVisible' => 1,
                    'moduleId' => $mid
            ))->result_array();
    
            $result = array();
            if ($ext_info) {
                foreach ($ext_info as $row) {
                    if($fields[$row['fieldId']]['fieldType'] == 'rich_image') {
                        // 对rich_image里面的元素排序
                        $m_value = json_decode($row['mValue'], true);
                        $rich_image_fields = $this->config->item('rich_image');
                        $value = $sort_field = array();
                        if($m_value) {
                            foreach($rich_image_fields as $k => $val) {
                                $sort_field[$k] = $val['key'];
                            }
                            foreach($m_value as $val) {
                                $arr = array();
                                foreach($sort_field as $k => $v) {
                                    $arr[$v] = $val[$v];
                                }
                                $value[] = $arr;
                            }
                        }
                        $result[$row['fieldId']] = $value;
                    } else {
                        $result[$row['fieldId']] = $row['mValue'];
                    }
                }
                unset($ext_info, $fields);
                
                $this->assign('value', $result);
            }
            $this->display('module_' . $mid, 'place');
        }
    }

    /**
     * 商品页面
     */
    function goods($id = 0, $t = 0) {
        $id = intval($id);
        ($id <= 0) && die('');
        // 获取团购参数 0 商品 1 团购 不带参数为0
        $t = intval($t);

        switch($t) {
            case 1 :
                $table = 'GrouponItem';
                $field = 'title as title, introduce as content';
                break;
            default :
                $table = 'Product';
                $field = 'name as title, introduce as content';
        }

        $row = $this->db->select($field)->where("id = '{$id}'")->get($table)->row_array();
        $this->assign($row);
        $this->display('goods', 'goods');
    }

    /**
     * 富文本编辑器里面的上传功能
     */
    function upload() {
        $file = $_FILES['filedata'];
        $data = array(
                'file_type' => 'common',
                'file' => '@' . $file['tmp_name'],
                'resolution' => 'odp'
        );
        $data['sign'] = rsa_sign();
        $upload_url = $this->config->item('v3_upload_image_api');

        // $ch = curl_init();
        // // 设置参数
        // curl_setopt($ch, CURLOPT_URL, $upload_url['transfer_image']);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // true 返回值 false 显示执行结果
        // // 执行
        // $result = curl_exec($ch);
        // curl_close($ch);
        $result = http_request($upload_url['transfer_image'], $data, array(), 'POST', true);

        $json = json_decode($result, true);
        if ($json['result_code']) {
            // 上传失败
            die("{'err':'1','msg':{'url':'','localname':'{$file['name']}','id':'1'}}");
        } else {
            // 上传成功
            $image_url = image_url($json['file_name'], 'common', 'odp');
            die("{'err':'','msg':{'url':'{$image_url}','localname':'{$file['name']}','id':'1'}}");
        }
    }

    /**
     * 获取得到rsa的签名sign
     */
    function rsa_sign() {
        $sign = rsa_sign();
        echo $sign ? $sign : '';
    }
    
    function rec_test(){    	
		$post = array(
			'titles'=>array('test1','test2'),
			'linkes'=>array('http://in.jin95.com', 'http://in.jin95.com'),
			'summaries'=>array('test1的摘要'),
			'exp1'=>array('exp1-test1','exp1-test2'),
			'exp2'=>array(array(1,2),array(2,3)),
			'exp3'=>array(2,1),
			'exp4'=>array('exp4-txt-test1', 'exp4-txt-test2'),
			'exp5'=>array('2012-11-29','2012-11-30'),
			'exp6'=>array('2012-12-01 08:00:00', '2012-12-01 21:30:00'),
			'exp7'=>array('18:00'),
			'exp8'=>array('default_icon.png', 'default_icon.png'),
		);   
		$curl = curl_init();
		
    	curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, 'http://xr.lo.chengdu.cn/web/new_recommend/save_rec/fid/6');
        curl_setopt($curl, CURLOPT_POST, count($post));
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	    $content = curl_exec($curl);
	    $content === false && die('HTTP TIMEOUT ERROR');
	    $state = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    $state != 200 && die('HTTP REQUEST ERROR: '.$state.'<p>'.$content.'</p>');
	    curl_close($curl);
	    exit($content);
	 /*   $result = json_decode($content, true);
	    if(!$result['code'])
	    	die('SUCCESS: '.$result['msg']);
	    else
	    	die('ERROR: '.$result['msg']); */
    }
    
    /**
     * 碎片推荐的上传处理
     * Create by 2012-12-4
     * @author liuweijava
     * @return array(code, msg), code:0=上传成功，msg返回图片访问地址；1=图片格式不正确；2=保存到本地失败；3=保存到服务器失败
     */
    function rec_upload(){
    	if($this->is_post()){
    		$tw = $this->post('tw');
    		$tw = !empty($tw) ? intval($tw) : false;
    		$th = $this->post('th');
    		$th = !empty($th) ? intval($th) : false;
    		$sign = $this->post('sign');//SRA钥匙串
    		$type = $this->post('file_type');
    		empty($type) && $type = 'common';//文件类型
    		$resolution = $this->post('resolution');
    		empty($resolution) && $resolution = 'odp';//分辨率
    		if(!empty($_FILES) && !empty($_FILES['file'])){
    			$up_file = $_FILES['file'];
    			$local = FCPATH . '/data/upload/'.$up_file['name'];
    			//允许上传的
    			$allow_type = array('jpg', 'jpeg', 'png', 'gif');
    			$suffix = pathinfo($up_file['name'], PATHINFO_EXTENSION);
    			!in_array($suffix, $allow_type) && $this->error('图片格式不正确，仅支持上传：jpg, png和gif格式的图片');
    			//保存到本地
    			move_uploaded_file($up_file['tmp_name'], $local);
    			chmod($local, 0777);
    			//需要缩略的
    			if($tw || $th){
    				list($w, $h, $t) = getimagesize($local);
    				if($w > $tw || $h > $th){
    					$this->load->library('upload_image');
    					$scale = $w < $h ? $tw / $w : $th / $h;
	    				//先缩小宽度
	    				$local = $this->upload_image->resizeImage($local, $w, $h, $scale);
	    				//再裁切
	    				$local = $this->upload_image->resizeThumbnailImage($local, $local, $tw, $th, 0, 0, 1);
    				}
    			}
    			//上传到服务器
    			$this->lang->load('api', 'chinese');
    			$api_uri = $this->lang->line('api_upload_image');
    			$param = array(
    				'file' => '@'.$local,
    				'file_type' => $type,
    				'resolution' => $resolution
    			);
    			$header = array('uid', $this->auth['id']);
    			$result = json_decode(send_api_interface($api_uri, 'POST', $param, $header), true);
    			switch($result['result_code']){
    				case 0:
    					$result['file_name'] = image_url($result['file_name'], $type, $resolution);
    					//删除本地图片
    					unset($local);
    					break;
    				default:break;
    			}
    			$this->echo_json($result);
    		}
    	}else{
    		$this->error('非法请求');
    	}
    }

}
