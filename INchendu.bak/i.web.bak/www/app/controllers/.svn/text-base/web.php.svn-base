<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 网站主Controller
 * 一些公用的碎片页面
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-4-28
 */

class Web extends MY_Controller {
    
    /**
     * 首页访问
     * @param $force 强制更新
     * @param $generate 生成页面
     */
    function index($force = '', $generate = '') {
        $fragment = get_data('fragment');
        
        $fids = array();
        foreach($fragment as $id => $f) {
            if($f['parentId'] == 1 && empty($f['autoUpdate'])) {
                // 首页的推荐信息
                $fids[] = $id;
            }
        }
        
        $this->load->helper('recommend');
        $data = get_recommend_data($fids);
        $this->assign('data', $data);
        $force && $this->assign('force', $force);
        if($generate) {
            $this->assign('generate', $generate);
            $html = $this->fetch('index');
            file_put_contents(FCPATH . 'index.html', $html);
            echo $html;
        } else {
            $this->display('index');
        }
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
    	$app_jump = sprintf('inchengdu://%s/%d', $type, $id);
    	if($type === 'ingroupon'){
    		//检查团购来源
    		$query = $this->db->where(array('id'=>$id))->like('sourceName', '买购')->get('GrouponItem')->first_row('array');
    		if(!empty($query)){//是买购的团购
    			$this->assign(array('is_mygo'=>1, 'mygo_jump'=>'http://mygo.chengdu.cn/index.php?controller=mobile&action=detail&team_id='.$query['originalId']));
    		}
    	}
    	$this->assign(array('app_jump'=>$app_jump, 'type'=>$type));
    	$this->display('qr');
    }
    
    function test($id = '', $name = '') {
    	
    	$size = array();
    	
    	for($i=0;$i<100;$i++){
    		$size[] = $i+1;
    	}
    	$len = 30;
    	for($i = 0;$i<$len;$i++){
    		$out = array();
    		echo '<p>'.($i+1).'<br/>'.count($size).':';
    		$ln = $i == $len - 1 ? count($size) : ceil(count($size) * 0.3);
    		echo $ln.'<br/><br/>';
    		$s = 0;
    		while($s < $ln){
    		//	array_unique($size);
    			$ix = rand(0, count($size)-1);
    			$out[] = $size[$ix].'';
    			unset($size[$ix]);
	    		$s++;
    		}
    		echo '['.implode(',',$out).']</p>';
    //		unset($out);
    	}
    	echo '<p>Size: ['.print_f($size, true).']</p>';
    	exit;
    	
    	$str = '这里六个汉字';
    	exit(mb_strlen($str).':'.strlen($str));
    	
    		$url = 'http://xr87.in.chengdu.cn/main/place/311176';
    		
    		$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			$html = curl_exec($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if($code == 200){
				$html = str_replace(array('<html>', '</html>'), array('',''), $html);
				$body = preg_replace("/.*?<body>(.*?)<\/body>/si", "$1", $html);
			/*	$list = array();
				$dom = new DomDocument();
				$dom->loadHTML($html);
				
				$body = $dom->getElementsByTagname('body')->item(0);
				var_dump($body);*/
				exit($body);
				
				/*$dls = $dom->getElementsByTagname('dl');
				for($i=0;$i<$dls->length;$i++){
					$dl_node = $dls->item($i);
					//获得DT的内容
					$dt = $dl_node->getElementsByTagname('dt')->item(0);
					//获取DD的内容
					$dd = $dl_node->getElementsByTagname('dd')->item(0);
					//判断是否有图片
					$imgs = $dd->getElementsByTagname('img');
					if(isset($imgs) && !empty($imgs)){
						$attrs = $imgs->item(0)->attributes;
						if(!empty($attrs)){
							$value['is_img'] = 1;
							foreach($attrs as $attr){
								if($attr->name === 'src'){
									$value['src'] = $attr->value;
									break;
								}
							}
						}else{
							$value['is_img'] = 0;
							$value['src'] = $dd->nodeValue;
						}
					}else{
						$value = array(
							'is_img' => 0,
							'src' => $dd->nodeValue
						);
					}
					$list[$dt->nodeValue] = $value;
				}
				exit(print_r($list));*/
				
				/*$xml = new DOMXpath($dom);
				$dts = $xml->query("//dl/dt");
				$dds = $xml->query("//dl/dd");
				$imgs = $xml->query("//dl/dd/img");
				for($i=0;$i<$dts->length;$i++){
					if($imgs->item($i) != null){
						foreach($imgs->item($i)->attributes as $attr){
							if($attr->name === 'src'){
								$list[$dts->item($i)->nodeValue] = array('src'=>$attr->value,'is_img'=>1);
							}
						}
					}else{
						$list[$dts->item($i)->nodeValue] = array('src'=>$dds->item($i)->nodeValue, 'is_img'=>0);
					}
				}
				exit(var_dump($list));*/
			}else{
				exit('request error');
			}
    	
        $this->load->library('api_sender');
        
     /*   $str = 'http://192.168.1.40/private_api/oauth/bind?stage_code=0&uid=83';
        $str = $this->api_sender->des_encrypt($str);
        echo $str.'<p/><p/>';
        echo $this->api_sender->des_decrypt($str);
        exit; */
        
        $params = array(
        	'api'=>'oauth/bind',
        	'attr'=>array('stage_code'=>0,'uid'=>83),
        	'has_return'=>TRUE
        );
        echo 'POST REQUEST<p/>';
        $this->api_sender->post_api($params);
        echo 'GET REQUEST<p/>';
        $this->api_sender->get_api($params);
    }
    
    /**
     * 关于我们
     * Create by 2012-5-21
     * @author liuw
     */
    public function about(){
    	$this->assign('op', 'about');
    	$this->display('about');
    }
    
    /**
     * 联系我们
     * Create by 2012-5-21
     * @author liuw
     */
    public function contact(){
    	$this->assign('op', 'contact');
    	$this->display('contact');
    }
    
    /**
     * 帮助  
     * Create by 2012-5-21
     * @author liuw
     */
    public function help(){
    	$this->assign('op', 'help');
    	$this->display('help');
    }
    
    /**
     * 使用条款
     * Create by 2012-5-21
     * @author liuw
     */
    public function privacy(){
    	$this->assign('op', 'privacy');
    	$this->display('privacy');
    }
    
    /**
     * 招聘
     * Create by 2012-5-21
     * @author liuw
     */
    public function jobs(){
    	$this->assign('op', 'jobs');
    	$this->display('jobs');
    }
    
    /**
     * 图片墙 
     * Create by 2012-6-5
     * @author liuw
     * @param int $page
     */
    public function photo($page=1){
    	//查询图片总数 
    	$count = $this->db->where(array('type'=>$this->config->item('post_photo'), 'status < '=>2))->count_all_results('Post');
    	if($count){
    		$parr = paginate('/photo', $count, $page, FALSE, 15);
    		//查数据 
    	//	$sql = 'SELECT p.*, pl.placename, u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname, u.username) AS uname FROM Post p INNER JOIN Place pl ON pl.id=p.placeId INNER JOIN User u ON u.id=p.uid WHERE p.type=? AND p.status < 2 ORDER BY p.createDate DESC LIMIT ?, ?';
    		$sql = 'SELECT  p.*, pl.placename, pl.isVerify, u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname, u.username) AS uname, u.isVerify AS u_isverify FROM ( select  id , placeId , uid from Post where  type=? AND status < 2 ORDER  BY createDate DESC LIMIT ?, ?) a INNER JOIN Post p ON a.id = p.id INNER JOIN Place pl ON a.placeId = pl.id INNER JOIN User u ON a.uid = u.id';
    		
    		$arr = array($this->config->item('post_photo'), $parr['offset'], $parr['per_page_num']);
    		$list = array();
    		$query = $this->db->query($sql, $arr)->result_array();
    		foreach($query as $row){
    			//时间
    			//$row['createDate'] = substr($row['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($row['createDate']))*/;
    			//图片
    			$row['photo'] = empty($row['photoName']) ? '':image_url($row['photoName'], 'user', 'thweb');
    			if($row['photo']) {
    			    $wh = image_wh($row['photoName']);
                    $row['w'] = $wh['w'];
                    $row['h'] = $wh['h'];
    			}
    			//头像
    			$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
    			//分享统计
    			$f_count = $this->db->where(array('itemId'=>$row['id'], 'itemType'=>$row['type']))->count_all_results('UserFavorite');
    			$row['favoriteCount'] = $f_count;
    			//获取图片尺寸
    			/*if(!empty($row['photo'])){
    				list($w, $h, $t, $a) = @getimagesize($row['photo']);
    				$row['photo_w'] = $w;
    				$row['photo_h'] = $h;
    			}else{
    				$row['photo_w'] = 200;
    				$row['photo_h'] = 133;
    			}*/
    			//是否已收藏 
    			if(!empty($this->auth)){
    				$favorite = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserFavorite');
    				if($favorite > 0)
    					$row['favorite'] = $favorite;
    			}
    			//是否已赞 
    			if(!empty($this->auth)){
    				$praise = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserPraise');
    				if($praise > 0)
    					$row['praise'] = $praise;
    			}
				$row['content'] = htmlspecialchars($row['content']);
    			$list[$row['id']] = $row;
    		}
    		$this->assign('list', $list);
    	}
    	
    	$this->display('photo');
    }
    
    /**
     * 上传照片到网站服务器
     * Create by 2012-6-12
     * @author liuw
     */
    public function upload(){
    	$cfg = $this->config->item('image_cfg');
    	$upload_path = $cfg['upload_path'];
    	if(!empty($_FILES)){
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $upload_path;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
				move_uploaded_file($tempFile,$targetFile);
			//	@chmod(0777, $targetFile);
				$this->load->library('upload_image');
				//压缩原图，短边固定到360,长边等比例压缩
				list($w, $h, $t, $at) = getimagesize($targetFile);
				$scale = $w < $h ? 360 / $w : 360 / $h;
				$this->upload_image->resizeImage($targetFile, $w, $h, $scale);
				//裁切原图，生成360*360的原图
				list($w, $h, $t, $at) = getimagesize($targetFile);
				if($w == 360){
					$s_w = 0;
					$s_h = ($h - 360) / 2;
					$scale = 360 / $h;
				}else{
					$s_w = ($w - 360) / 2;
					$s_h = 0;
					$scale = 360 / $w;
				}
				$this->upload_image->resizeThumbnailImage($targetFile, $targetFile, 360, 360, $s_w, $s_h, 1);
				exit("1");
			} else {
				exit('Invalid file type.');
			}
    	}
    }
    
    function online() {
        $this->display('online');
    }
    
    function css_replace() {
        // 读取CSS目录，替换里面的路径。
        $d = dir(FCPATH . './css');
        while(false !== ($file = $d->read())) {
            if(strpos($file, '.') !== 0) {
                $fpath = FCPATH . './css/' . $file;
                $str = file_get_contents($fpath);//css里面的图片相对路径转绝对路径
                $str = str_replace('..', '', $str);
                file_put_contents($fpath, $str);
            }
        }
        $d->close();
    }
    
    /**
     * 跳转转向，便于记录点击数量等等
     */
    function redirect($url = '/') {
        // 暂时
        $url = urldecode($url);
        
        strpos($url, 'http://') === 0 && forward($url);
    }
    
    /**
     * 查询最新的客户端版本
     * Create by 2012-9-3
     * @author liuw
     * @param int $platform,0=IOS，1=ANDROID，2=WP
     */
    function get_update($platform=0){
    	$data = $this->db->where('type', $platform)->order_by('createDate', 'desc')->limit(1)->get('ClientVersion')->first_row('array');
    	$this->echo_json($data);
    }
}
