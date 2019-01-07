<?php
/**
 * Create by 2012-11-27
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class New_recommend extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('fragment_model', 'frag');//碎片数据模型
		$this->load->model('recommend_model', 'rec');//推荐数据模型
		$this->load->helper('recommend_helper');//公共函数库
	}
	
	/**
	 * 内容推荐入口
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function index(){
		$fid = $this->get('fid');
		empty($fid) && $this->error('请选择碎片', '', '', 'closeCurrent');
		$frag = $this->frag->get_frag($fid);
		
		!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
		!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
		//数据源
		!empty($frag['dataSource']) && strpos($frag['dataSource'], 'http://') !== false && $frag['dataSource'] = $frag['dataSource'];
		//已推荐数据列表
		//var_dump($frag['rule']);
		$old_reces = $this->rec->old_rec_datas($fid);
		$max = $frag['rule']['max_length'];
		$frag = encode_json($frag);
		$this->assign(compact('frag', 'old_reces','max'));
		$this->display('new_rec', 'recommend');
	}
	
	/**
	 * 获取备选列表
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function alternatives(){
		$link = base64_decode($this->get('link'));
		//CURL访问数据源，获得备选列表
		$curl = curl_init();
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	    $json = curl_exec($curl);
	    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    $json === false && $this->echo_json(array('code'=>1, 'msg'=>'连接超时了'));
	    intval($status) != 200 && $this->echo_json(array('code'=>2, 'msg'=>'获取备选列表失败了'));
	    curl_close($curl);	 
	    $this->echo_json(array('code'=>0, 'datas'=>json_decode($json, true)));   
	}
    
    /**
     * 获取网站的站内内容
     * Create by 2012-11-27
     * @author liuweijava
     */
    function get_web_data(){
    	$type = $this->get('type');
    	$fid = $this->get('fid');
    	$pt = $this->get('pt');//搜索条件
    	$key = $this->get('key');//搜索关键词
    	$datas = $this->rec->ds_datas($type, $fid, $pt, $key);
    	echo encode_json($datas);
    	exit;                                                                                                                                                                                                                                                                                                                                                   
    }
	
	/**
	 * 获取已推荐列表
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function rec_list(){
		$fid = $this->get('fid');
		empty($fid) && $this->echo_json(array('code'=>1, 'msg'=>'请选择碎片'));
		$datas = $this->rec->old_rec_datas($fid);
		$list = array();
		empty($datas) && $datas = array();
		if(!empty($datas)){//格式化数据
			foreach($datas['datas'] as $k=>$d){
				//$white_space_partten = "/\n/";
				//$intro = preg_replace($white_space_partten,"[white_space]",$d['summary']);
				//基础属性
				$fd = array(
					'id' => $d['orderValue'],
					'title' => $d['title'],
					'title_link' => $d['link'],
					'intro' => $d['summary']
				);
				if(!empty($d['image'])){
					$img = pathinfo($d['image'], PATHINFO_BASENAME);
					$fd['image'] = $img;
					$fd['image_url'] = $fd['image_link'] = $d['image'];
				}
				//扩展属性
				if(!empty($d['extraData'])){
					$ed = json_decode($d['extraData'], true);
					foreach($ed as $k=>$e){
						$fd['extra'][$k] = $e;
					}
				}
				$list[] = $fd;
			}
		}
		//var_dump($list);exit;
		$this->echo_json($list);
	}
	
	/**
	 * 保存推荐数据
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function save_rec(){
		$fid = $this->get('fid');
		empty($fid) && $this->echo_json(array('code'=>9, 'msg'=>'请选择碎片'));
		if($this->is_post()){
			$code = $this->rec->save_data($fid);
			update_cache("web","data","fragmentdata",$fid);
			switch($code){
				case 1:$msg = '选择的碎片不存在';break;
				case 2:$msg = '保存推荐数据失败了';break;
				case 3:$msg = '您要保存的列表中有1条或以上数据没有设置标题，请修改。';break;
				case 4:$msg = '您要保存的列表中有1条或以上数据没有设置链接，请修改。';break;
				case 5:$msg = '保存的数量已经超过了设定的最大数，请删减内容或者修改碎片的最大推荐数';break;
				case 6:$msg = '神马都没有填，就不要提交了，标题和链接都是必填的。';break;
				default:$msg = '推荐数据已成功保存';break;
			}
			$this->echo_json(compact('code', 'msg'));
		}
	}
	
	/**
	 * 打开推荐数据编辑窗口
	 * Create by 2012-11-30
	 * @author liuweijava
	 */
	function open_editor(){
		$fid = $this->get('fid');
		empty($fid) && $this->error('请选择碎片');
		$frag = $this->frag->get_frag($fid);
		!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
		!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
		//数据源
	//	!empty($frag['dataSource']) && strpos($frag['dataSource'], 'http://') !== false && $frag['dataSource'] = $frag['dataSource'];
		$frag = encode_json($frag);
		$sel = $this->get('sel');
		!empty($sel) && $sel = urldecode(base64_decode($sel));
		
		$this->assign(compact('frag', 'sel'));
		$this->display('rec_editor', 'recommend');
	}
	
}   
   
 // File end