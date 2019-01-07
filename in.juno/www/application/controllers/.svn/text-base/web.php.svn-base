<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * web
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-7
 */

class Web extends Controller {
	
	/**
	 * IN成都首页
	 * Create by 2012-12-25
	 * @author liuweijava
	 */
    function index() {
        $passport_config = $this->config->item('passport');      
                
        get_salt();
        
 		$this->assign('passport_signin_url', $passport_config['signin_url']);
 		$this->assign('passport_sso_url', $passport_config['sso_url']);
 		$this->assign('passport_signup_url', $passport_config['signup_url']);
 		$this->assign('passport_taboo_url', $passport_config['taboo_url']);
 			
        $this->display('signin');
    }
    
    /**
     * 显示不同的客户端下载页面 
     * Create by 2012-12-29
     * @author liuweijava
     * @param string $platform
     */
    function download($platform='iphone'){
    	$this->config->load('config_common');
    	//获得client type
    	$type = $this->config->item('client_id')[$platform];
    	//从数据库获取最新的版本信息
    	$this->load->model('clientversion_model', 'm_cv');
    	$version = $this->m_cv->get_last_version($type);
    	$this->assign(compact('version', 'platform'));
    	$this->display($platform);
    }
    
    /**
     * android下载
     * @param $t  0 :ios 1:android
     */
    function d($t = 0) {
    	$this->load->model('clientversion_model', 'm_cv');
    	$version = $this->m_cv->get_last_version($t);
    	redirect($version['downloadUri'], 'GET', 301);
    }
    
    /**
     * 二维码桥接
     * Create by 2012-10-22
     * @author liuw
     * @param string $type
     * @param int $id
     */
    function qr($type, $id){
    	$this->load->library('user_agent');
    	$app_jump = sprintf('inshenyang://%s/%s%s', $type, $id, $_SERVER['QUERY_STRING']?('?'.$_SERVER['QUERY_STRING']):'');
//     	if($type === 'ingroupon'){
//     		//检查团购来源
//     		$query = $this->db->where(array('id'=>$id))->like('sourceName', '买购')->get('GrouponItem')->first_row('array');
//     		if(!empty($query)){//是买购的团购
//     			$this->assign(array('is_mygo'=>1, 'mygo_jump'=>'http://mygo.chengdu.cn/index.php?controller=mobile&action=detail&team_id='.$query['originalId']));
//     		}
//     	}
    	$this->assign(array('app_jump'=>$app_jump, 'type'=>$type));
    	$this->display('qr');
    }
}
