<?php
/**
 * api接口调用
 * Create by 2012-5-31
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Api_sender{
	var $api_serv;//api接口访问地址
	var $api_folder;
	var $des;
	
	function __construct(){
		global $CI;
		$this->api_folder = $CI->config->item('api_folder');
		$this->api_serv = $CI->config->item('api_serv').$this->api_folder;
		// include 'Crypt/Crypt3DES.php';
		// $this->des = new Crypt3DES();
	}
	
	/**
	 * get方法调用api接口
	 * Create by 2012-5-31
	 * @author liuw
	 * @param array $params
	 * <div>数组结构:<ul>
	 * <li>api:string,接口名称/接口方法</li>
	 * <li>attr:array,需要传递的参数列表</li>
	 * <li>has_return:boolean,是否有返回内容</li>
	 * <li>uid:int,当前登录用户的用户id</li>
	 * </ul></div>
	 */
	function get_api($params){
		$api_url = $this->api_serv.$params['api'];
		$api_f = $this->api_folder.$params['api'];
		//构造query string
		if(!empty($params['attr']) && is_array($params['attr'])){
			$query_str = http_build_query($params['attr']);
			$api_f .= '?'.$query_str;
			$api_url .= '?'.$query_str;
		}
		$query = $this->openssl_encrypt();
		$api_url .= '&sign='.urlencode($query);
		//构造curl
		$curl = curl_init($api_url);
		if($params['uid'] > 0){
			$headers = array('X-INID'=>$params['uid']);
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		$content = curl_exec($curl);//执行get请求
		//http code 
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);//关闭curl句柄
		switch($http_code){
			case '404':$content = json_encode(array('code'=>1, 'msg'=>'PAGE NOT FOUND'));break;
			case '500':$content = json_encode(array('code'=>1, 'msg'=>'Server Error'));break;
			case '200':break;
			default:$content = json_encode(array('code'=>1, 'msg'=>'Unknow Error'));break;
		}
		return $params['has_return'] ? $content : TRUE;
	}	
	
	/**
	 * post方法调用api接口
	 * Create by 2012-5-31
	 * @author liuw
	 * @param array $params
	 * <div>数组结构:<ul>
	 * <li>api:string,接口名称/接口方法</li>
	 * <li>attr:array,需要传递的参数列表</li>
	 * <li>has_return:boolean,是否有返回内容</li>
	 * <li>uid:int,当前登录用户的用户id</li>
	 * </ul></div>
	 */
	function post_api($params){
		$api_url = $this->api_serv.$params['api'];
		//构造query string
		if(is_array($params) && $params['attr']['uploaded_file']){
		    // 上传图片
		    
			// if(){
				// $query_str['sign'] = $this->openssl_encrypt();
                // $query_str = http_build_query($params['attr']);
			// }else{//上传图片
// 				
				// $query_str['sign'] = $this->openssl_encrypt();
			// }
			
		} else {
		    
		}
		//构造curl
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $api_url);
		curl_setopt($curl, CURLOPT_POST, count($params['attr']));
		if($params['uid'] > 0){
			$headers = array('X-INID'=>$params['uid']);
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		if(!empty($query_str)) {
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $query_str);
		}
			
		$content = curl_exec($curl);//执行
		//http code 
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);//关闭curl句柄
		switch($http_code){
			case '404':$content = json_encode(array('code'=>1, 'msg'=>'PAGE NOT FOUND'));break;
			case '500':$content = json_encode(array('code'=>1, 'msg'=>'Server Error'));break;
			case '200':$content = $content;break;
			default:$content = json_encode(array('code'=>1, 'msg'=>'Unknow Error'));break;
		} 
		return $params['has_return'] ? $content : TRUE;
	}
	
	function des_encrypt(){
		//获得系统秒数
		list($usec, $sec) = explode(' ', microtime());
		$second = intval($sec.''.(intval($usec*1000)));
		return $this->des->encrypt($second);
	}
	
	function des_decrypt($string){
		return $this->des->decrypt($string);
	}
	
	/**
	 * 通过openssl实现rsa加密
	 * Create by 2012-6-1
	 * @author liuw
	 * @param string $string
	 * @return string
	 */
	function openssl_encrypt(){
		global $CI;
		//获得系统秒数
		// list($usec, $sec) = explode(' ',microtime());
		// $second = intval($sec.''.(intval($usec*1000)));
		$second = number_format(microtime(true)*1000, 0, '', '');
		$r = openssl_private_encrypt($second, $return, file_get_contents($CI->config->item('rsa_private_key_path')));
		return $r ? base64_encode($return) : FALSE;
	}
	
	/**
	 * 通过openssl实现rsa解密
	 * Create by 2012-6-1
	 * @author liuw
	 * @param string $string
	 * @return string
	 */
	function openssl_decrypt($string){
		global $CI;
		$r = openssl_public_decrypt(base64_decode($string), $return , file_get_contents($CI->config->item('rsa_public_key_path')));
		return $r ? $return : FALSE;
	}
	
	function openssl_sign(){
		global $CI;
		$key = base64_decode('5L2g5aa555qEUlNB');
		$r = openssl_sign($key, $return, file_get_contents($CI->config->item('rsa_private_key_path')));
		return base64_encode($return);
	}
}   
   
 // File end