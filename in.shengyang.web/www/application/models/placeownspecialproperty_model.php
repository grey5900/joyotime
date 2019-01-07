<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PlaceOwnSpecialProperty表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Placeownspecialproperty_Model extends MY_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取地点扩展属性的详情
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $id
	 */
	public function get_info($id){
		//扩展属性基本信息
		$info = $this->db->where(array('id'=>$id, 'status'=>0))->limit(1)->get($this->_tables['placeownspecialproperty'])->first_row('array');
		//图片
		if(!empty($info['images'])){
			$images = explode(',', $info['images']);
			$info['images'] = array();
			foreach($images as $img){
				$info['images'][] = image_url($img, 'common');
			}
			unset($images);
		}
		//扩展属性详情
		$this->db->select($this->_tables['placemoduledata'].'.mValue, '.$this->_tables['placemodulefield'].'.*');
		$this->db->join($this->_tables['placemoduledata'], $this->_tables['placemoduledata'].'.fieldId='.$this->_tables['placemodulefield'].'.fieldId AND '.$this->_tables['placemoduledata'].'.moduleId='.$this->_tables['placemodulefield'].'.moduleId', 'left');
		$datas = $this->db->where(array($this->_tables['placemodulefield'].'.moduleId'=>$info['moduleId'], $this->_tables['placemoduledata'].'.placeId'=>$info['placeId'],$this->_tables['placemoduledata'].'.isVisible'=>1))->order_by($this->_tables['placemodulefield'].'.orderValue', 'desc')->get($this->_tables['placemodulefield'])->result_array();
		foreach($datas as &$row){
			//格式化数据,主要是处理图片
			if(!empty($row['mValue'])){
				switch($row['fieldType']){
					case 'image':
						$row['mValue'] = image_url($row['mValue'], 'common');
						break;
					case 'rich_image':
						$images = decode_json($row['mValue']);
						if(is_array($images)){
							foreach($images as &$img){
								$img['image'] = image_url($img['image'], 'common');
								unset($img);
							}
						}else
							$images = image_url($images, 'common');
						$row['mValue'] = $images;
						unset($images);
						break;
					default:
						break;
				}
			}
			unset($row);
		}
		$info['datas'] = $datas;
		unset($datas);
		return $info;
	}
	
}