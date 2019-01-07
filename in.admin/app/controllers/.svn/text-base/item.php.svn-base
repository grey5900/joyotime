<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
  * 道具管理
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-6
  */

class Item extends MY_Controller {
	

	function __construct(){
		parent::__construct();
		$this->allowAction = array(1,2);
		
	}
    /**
     * 道具派发列表
     */
    function present_list() {
        $type = trim($this->post('type'));
        $keywords = trim($this->post('keywords'));
        $where_sql = 'a.sender = 56 AND a.itemId = b.id AND a.reciever = c.id';
        if($keywords !== '') {
            switch($type) {
                case 'nickname':
                    $where_sql .= ' AND c.nickname like \'%' . $keywords . '%\'';
                    break;
                case 'uid':
                    $where_sql .= ' AND a.reciever = \'' . $keywords . '\'';
                    break;
                default:
                    $where_sql .= ' AND b.name like \'%' . $keywords . '%\'';
            }
            
            $keywords = dstripslashes($keywords);
        }
        $total_num = $this->db2->from('ItemMessage a, Item b, User c')
                            ->where($where_sql, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db2->select('a.*, b.name as itemName, c.username, c.nickname', false)
                        ->order_by('a.createDate', 'desc')
                        ->limit($paginate['per_page_num'], $paginate['offset'])
                        ->where($where_sql, null, false)
                        ->from('ItemMessage a, Item b, User c')
                        ->get()->result_array();
        
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('present_list');
    }
    
    /**
     * 派发
     */
    function present() {
        if($this->is_post()) {
            // 提交派送
            $id = intval($this->post('item'));
            $uid = intval($this->post('content_id'));
            $message = $this->post('message');
            
            if($id <= 0 || $uid <=0) {
                $this->error('提交数据错误，请至少选择用户和道具');
            }
            
            if(cstrlen($message) > 255) {
                $this->error('附言最多255个字哦。亲');
            }
            
            $rtn = json_decode(request_api('/props/present', 'POST', 
                            compact('id', 'uid', 'message'), array('uid' => 56)), true);
            
            $rtn['result_code']?$this->error('派送失败，请重试，亲'):$this->success('派送成功', 'item_present_list', '/item/present_list', 'closeCurrent');
        }
        
        // 获取道具列表
        $items = $this->db2->select('id, name')->get($this->_tables['item'])->result_array();
        $this->assign('items', $items);
        
        $this->display('present');
    }
    
    function recycle(){
    	
    	$items = $this->db->get($this->_tables['item'])->result_array();
    	
    	$this->assign(compact('items'));
    	$this->display('recycle');
    }
    
    function itemAwardsForObject(){
    	
    	$keywords = $this->input->post("keywords");
    	$type = $this->input->post("type");
    	$itemAwardActionId = intval($this->input->post("itemAwardActionId"));
    	
    	$where = " 1=1 ";
    	if($keywords){
	    	switch($type){
	    		case 'poi':
	    			$where .= " AND ( iaafo.itemId=".intval($keywords)." or p.placename like '%".$keywords."%')";
	    			break;
	    		case 'item_name' :
	    			$where .= " AND i.name like '%".$keywords."%' ";
	    			break;
	    	}
    	}
    	if($itemAwardActionId){
    		$where .= " AND iaafo.itemAwardActionId=".$itemAwardActionId;	
    	}
    	
    	$this->db->where($where,null,false);
    	if($type == 'item_name'){
    		$this->db->join($this->_tables['objectownawards']." ooa","ooa.iaafoId=iaafo.id",'left')
    				 ->join($this->_tables['item'].' i','i.id=ooa.itemId','left');
    	} 
    	$total = $this->db->from($this->_tables['itemawardactionforobject']." iaafo")
    					  ->join($this->_tables['place'].' p','p.id = iaafo.itemId and iaafo.itemType=1','left')    			
    					  ->count_all_results();
    	$paginate = $this->paginate($total);
    	
    	$this->db->select('iaafo.*,iaa.actionName,p.placename,p.id pid');
    	$this->db->from($this->_tables['itemawardactionforobject']." iaafo");
    	$this->db->join($this->_tables['itemawardaction'].' iaa','iaafo.itemAwardActionId = iaa.id','left');
    	$this->db->join($this->_tables['place'].' p','p.id = iaafo.itemId and iaafo.itemType=1','left');
    	if($type == 'item_name'){
    		$this->db->join($this->_tables['objectownawards']." ooa","ooa.iaafoId=iaafo.id",'left')
    				 ->join($this->_tables['item'].' i','i.id=ooa.itemId','left');
    	} 
    	$this->db->where($where,null,false);
    	$this->db->limit($paginate['per_page_num'], $paginate['offset']);
    	$list = $this->db->get()->result_array();
    	
    	foreach($list as &$row){
    		$this->db->select('ooa.*,i.name,count(ial.itemId) as bao');
    		$this->db->from('ObjectOwnAwards ooa');
    		$this->db->join('Item i','i.id = ooa.itemId','left');
    		$this->db->join('ItemAwardLog ial','ial.itemId = ooa.itemId and ial.iaafoId='.$row['id'],'left');
    		$this->db->where('ooa.iaafoId',$row['id']);
    		$this->db->group_by('ooa.itemId');
    		$items = $this->db->get()->result_array();
    		$row['items'] = $items;
    		unset($items);
    	}
    	$actions = $this->db->where_in('id',$this->allowAction)->get($this->_tables['itemawardaction'])->result_array();
    	$this->assign(compact('list','type','keywords','actions','itemAwardActionId'));
    	$this->display('iaafo');
    }
    function del_iaafo(){
    	$id = intval($this->get('id'));
    	if($id){
    	$b = $this->db->where('iaafoId',$id)->delete($this->_tables['objectownawards']);
    	$b && $b = $this->db->where('id',$id)->delete($this->_tables['itemawardactionforobject']);
    	
    	if($b){
    		api_update_cache($this->_tables['itemawardactionforobject']);
    		$this->success('删除成功');
    	}
    	else{
    		$this->error('删除失败，请稍后再试！');
    	}
    	}
    	else{
    		$this->error('你没有选择要删除的数据！');
    		
    	}
    }
    function add_iaafo(){
    	$id = $this->get('id');
    	if($this->is_post()){
    		$place_id = $this->post('poi_id');
    		$itemAwardActionId = $this->post('itemAwardActionId');
    		$probability = $this->post('probability');
    		$frequency = $this->post('frequency');
    		
    		$quantities = $this->post('quantity');
    		$itemids = $this->post('itemId');
    		
    		
    		if(empty($quantities)){
    			$this->error('请添加物品!');
    		}
    		if(empty($itemAwardActionId)){
    			$this->error('请选择动作!');
    		}
    		
    		/*foreach($quantities as $row){
    			if(intval($row) <= 0){
    				$this->error('所有库存都必须大于0,请确认!');
    			}
    		}*/
    		
    		$iaafo_data = array(
    						'itemAwardActionId' => $itemAwardActionId,
    						'itemType' => 1, //POI
    						'itemId' => $place_id,
    						'probability' => $probability,
    						'frequency' => $frequency
    					);
    		//$id = $this->db->insert($iaafo_data);
    		$b = true;
    		if($id){
    			$op = '编辑';
    			$b = $this->db->where('id',$id)->update($this->_tables['itemawardactionforobject'],$iaafo_data);
    		}
    		else{
    			$op = '添加';
    			//检查是否已经存在
    			$ex = $this->db->where('itemAwardActionId',$itemAwardActionId)->where('itemType',1)->where('itemId',$place_id)->get($this->_tables['itemawardactionforobject'])->row_array();
    			if($ex){
    				$this->error('这个POI对应的动作已经存在了，请不要重复添加！');
    			}
    			$b = $this->db->insert($this->_tables['itemawardactionforobject'],$iaafo_data);
    			$id = $this->db->insert_id();
    		}
    		
    		if($id && $b){
    			//先删除
    			$this->db->where('iaafoId',$id)->delete($this->_tables['objectownawards']);
    			
    			$batch_ooa_item_data = array();
    			foreach($itemids as $k=>$row){
    				$temp = array(
    					'iaafoId' => $id,
    					'itemId' => $row,
    					'probability' => $quantities[$k],
    					'frequency' => $quantities[$k],
    					'quantity' => $quantities[$k]
    				);
    				$batch_ooa_item_data[] = $temp;
    			}//insert_batch
    			$bb = $this->db->insert_batch($this->_tables['objectownawards'],$batch_ooa_item_data);
    			if($bb){
    				api_update_cache($this->_tables['itemawardactionforobject']);
    				$this->success('添加成功','','','closeCurrent');
    			}
    			else{
    				$this->error('添加道具失败了，请稍后再试！');
    			}
    		}else{
    			$this->error($op.'失败,请稍后再试！');
    		}
    	}
    	$info = $this->db->select('iaafo.*,p.placename')
    					 ->from($this->_tables['itemawardactionforobject']." iaafo")
    					 ->join($this->_tables['place']." p","iaafo.itemId=p.id")
    					 ->where('iaafo.id',$id)->get()->row_array();
    	$iaafo_items = $this->db->select("ooa.*,i.name")
    							->from($this->_tables['objectownawards']." ooa")
    							->join($this->_tables['item']." i","i.id=ooa.itemId","left")
    							->where('iaafoId',$id)->get()->result_array();
    	$info['items'] = $iaafo_items;
    	if($iaafo_items){
    		$item_ids = array();
    		foreach($iaafo_items as $row){
    			$item_ids[] = $row['itemId'];
    		}
    		$this->db->where_not_in('id',$item_ids);
    		//var_dump($item_ids);
    	}
    	$items = $this->db->get($this->_tables['item'])->result_array();
    	
    	$actions = $this->db->where_in('id',$this->allowAction)->get($this->_tables['itemawardaction'])->result_array();
    	$this->assign(compact('items','actions','info'));
    	$this->display('add_iaafo');
    }
    
    function itemAwardLog(){
    	$keywords = $this->input->post("keywords");
    	$type = $this->input->post("type");
    	$itemAwardActionId = intval($this->input->post("itemAwardActionId"));
    	$iaafoId = intval($this->input->get("iaafoId")) ? intval($this->input->get("iaafoId")) : intval($this->input->post("iaafoId"));
    	$where = " 1=1 ";
    	if($keywords){
	    	switch($type){
	    		case 'poi':
	    			$where .= " AND ( (iaafo.itemId=".intval($keywords)." AND iaafo.itemType = 1) or p.placename like '%".$keywords."%')";
	    			break;
	    		case 'nickname' :
	    			$where .= " AND ( u.nickname like '%".$keywords."%' or u.username like '%".$keywords."%') ";
	    			break;
	    	}
    	}
    	if($iaafoId){
    		$where .= " AND ial.iaafoId = ".$iaafoId;
    	}
    	if($itemAwardActionId)
    	{
    		$where .= " AND ial.actionId =".$itemAwardActionId;
    	}
    	
    	
    	$total = $this->db->from($this->_tables['itemawardlog']." ial")
    					  ->join($this->_tables['item']." i","ial.itemId=i.id",'left')
    					  ->join($this->_tables['itemawardaction']." iaa","iaa.id=ial.actionId",'left')
    					  ->join($this->_tables['user']." u","u.id=ial.uid",'left')
    					  ->join($this->_tables['itemawardactionforobject']." iaafo","iaafo.id=ial.iaafoId",'left')
    					  ->join($this->_tables['place']." p","p.id=iaafo.itemId and iaafo.itemType=1",'left')
    					  ->where($where,null,false)
    					  ->count_all_results();
    	$paginate = $this->paginate($total);
    	
    	
    	$list = $this->db->select('ial.*,i.name,iaa.actionName,u.username,u.nickname,u.avatar,p.placename')
    				     ->from($this->_tables['itemawardlog']." ial")
    	                 ->join($this->_tables['item']." i","ial.itemId=i.id",'left')
    					 ->join($this->_tables['itemawardaction']." iaa","iaa.id=ial.actionId",'left')
    					 ->join($this->_tables['user']." u","u.id=ial.uid",'left')
    					 ->join($this->_tables['itemawardactionforobject']." iaafo","iaafo.id=ial.iaafoId",'left')
    					 ->join($this->_tables['place']." p","p.id=iaafo.itemId and iaafo.itemType=1",'left')
    					 ->limit($paginate['per_page_num'], $paginate['offset'])
    					 ->where($where,null,false)
    					 ->get()->result_array();
    	
    	$actions = $this->db/*->where_in('id',$this->allowAction)*/
    					->get($this->_tables['itemawardaction'])->result_array();
    	$this->assign(compact('list','type','keywords','iaafoId','actions','itemAwardActionId'));
    	$this->display('award_log');
    }
    
    function list_actions(){
    	$place_id = intval($this->post("poi"));
    	$infoid = intval($this->post("infoid"));
    	if($infoid){
    		$this->db->where_not_in("id",(array)$infoid);
    	}
    	$actions_exists = $this->db->select('group_concat(itemAwardActionId) as alist')->where('itemId',$place_id)->where('itemType',1)->get($this->_tables['itemawardactionforobject'])->row_array();
    	$this->db->where_in('id',$this->allowAction);
    	if($actions_exists['alist']){
    		$this->db->where_not_in("id",explode(",",$actions_exists['alist']));
    	}
    	
    	$actions = $this->db->get($this->_tables['itemawardaction'])->result_array();
    	echo json_encode($actions);
    	exit;
    }
    
    function lottery(){
    	if($this->is_post()){
    		$this->db->set('appearByLottery',0)->update($this->_tables['item']);
    		$items = $this->post('items');
    		$ia = $this->db->where_in('id',$items)->set('appearByLottery',1)->update($this->_tables['item']);
    		api_update_cache($this->_tables['item']);
    		if($ia){
    			$this->success('ok');
    		}
    		else{
    			$this->error('retry');
    		}
    	}
    	$items = $this->db->get($this->_tables['item'])->result_array();
    	
    	$this->assign(compact('items'));
    	$this->display('lottery');
    }
    function itemUseLog(){
    	if($this->is_post()){
    		$date = $this->post('date');
    		$items = $this->db->select('id, name')->get($this->_tables['item'])->result_array();
    		foreach($items as &$row){
    			$where = "createDate >= '{$date} 00:00:00' and createDate <= '{$date} 23:59:59'";
    			//使用次数
    			$use_count = $this->db->where('itemId',$row['id'])
    								  ->where($where,null,false)
    								  ->count_all_results($this->_tables['itemuselog']);
  				$row['use_count'] = $use_count;
  				//SELECT count(id) FROM ItemExchangeLog WHERE itemId = #需要查询的道具id#
  				//商城购买
  				$buy_count = $this->db->where('itemId',$row['id'])
    								  ->where($where,null,false)
    								  ->count_all_results($this->_tables['itemexchangelog']);
  				$row['buy_count'] = $buy_count;
  				//首页 6 抢地主失败1 扫描积分票2 YY 点评 分享 345
  				$count_things = $this->db->select('ial.actionId,count(ial.id) as total')
  						  ->from($this->_tables['itemawardlog']." ial")				
    					  ->join($this->_tables['itemawardaction']." iaa","iaa.id=ial.actionId",'left')
    					  ->where("ial.createDate >= '{$date} 00:00:00' and ial.createDate <= '{$date} 23:59:59'",null,false)
    					  ->where("ial.itemId",$row['id'])
    					  ->where_in('ial.actionId',array(1,2,3,4,5,6))
    					  ->group_by('ial.actionId')	
    					  ->get()				  
    					  //->count_all_results();
    					  ->result_array();
    			$action_total = array();
    			foreach($count_things as $value){
    				$action_total[$value['actionId']] = $value['total'];
    			}
    			
    			//var_dump($action_total);
    			$row['actions_count'] = $action_total;
    			unset($action_total);
    		}
    		$this->assign(compact('items','date'));
    	}
    	$this->display('item_use_log');
    }
}