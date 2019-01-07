<?php
/**
 * Create by 2012-6-4
 * @author liuw
 * @copyright Copyright(c) 2012-2014
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class Crypt3DES{
	var $key = "NOUWbbNtAR8Vm0nmGVK1v9yRN5QVrhAv";
	var $iv = "23456789";

	/**
	 * 加密
	 */
	public function encrypt($input){
		$input = $this->padding($input);
		$key = base64_decode($this->key);
		$td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
		//使用MCRYPT_3DES算法,cbc模式
		mcrypt_generic_init($td, $key, $this->iv);
		//初始处理
		$data = mcrypt_generic($td, $input);
		//加密
		mcrypt_generic_deinit($td);
		//结束
		mcrypt_module_close($td);
		//    $data = $this->remove_br(base64_encode($data));
		$data = base64_encode($data);
		return $data;
	}

	/**
	 * 解密
	 */
	public function decrypt($encrypted){
		$encrypted = base64_decode($encrypted);
		$key = base64_decode($this->key);
		$td = mcrypt_module_open( MCRYPT_3DES,'',MCRYPT_MODE_ECB,'');
		//使用MCRYPT_3DES算法,cbc模式
		mcrypt_generic_init($td, $key, $this->iv);
		//初始处理
		$decrypted = mdecrypt_generic($td, $encrypted);
		//解密
		mcrypt_generic_deinit($td);
		//结束
		mcrypt_module_close($td);
		$decrypted = $this->remove_padding($decrypted);
		return $decrypted;
	}

	/**
	 * 填充密码
	 */
	private function padding($str){
		$len = 8 - strlen( $str ) % 8;
		for ( $i = 0; $i < $len; $i++ )
		{
			//         $str .= chr(0);
			$str .= chr($len);
		}
		return $str ;
	}

	/**
	 * 移出填充符
	 */
	private function remove_padding($str){
		/*	$len = strlen( $str );
		 $newstr = "";
		 $str = str_split($str);
		 for ($i = 0; $i < $len; $i++ )
		 {
		 if ($str[$i] != chr( 0 ))
		 {
		 $newstr .= $str[$i];
		 }
		 }
		 return $newstr; */
		$padlen = ord(substr($str, (strlen($str)-1), 1));
		if ($padlen > 8 )return $str;

		for($i = -1*($padlen-strlen($str)); $i < strlen ($str); $i ++) {
			if (ord(substr($str, $i, 1)) != $padlen)return false;
		}

		return substr($str, 0, -1*($padlen-strlen($str)));
	}

	/**
	 * 移出回车
	 */
	private function remove_br($str){
		$len = strlen( $str );
		$newstr = "";
		$str = str_split($str);
		for ($i = 0; $i < $len; $i++ )
		{
			if ($str[$i] != '\n' and $str[$i] != '\r')
			{
				$newstr .= $str[$i];
			}
		}

		return $newstr;
	}
}
 
// File end