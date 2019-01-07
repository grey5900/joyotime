<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 生成news函数
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-3-2
 */

/**
 * 生成新闻频道下拉框选项
 * @param $data array
 * @param $option int(1,2...n) depth
 */ 

    /**
 * 生成带checkcbox的树形菜单
 * Create by 2012-3-8
 * @author liuw
 * @param array $rights
 */
function build_news_category($rights,$selected = 0,$self = 0,$isoptionGroup = false){
	$rtn_str = $name = $uri = $id = $submenu = $parentId = $catPath = $checked = '';
	$i = 0;
	foreach($rights as $key=>$r){
		switch($key){
			case 'catName':$name = $r;break;
			case 'id':$id = $r;break;
			case 'parentId':$parentId = $r;break;
			case 'checked':$checked = $r;break;
			case 'catPath':$catPath = $r;break;
			default:
				if(is_int($key))
					$submenu .= build_news_category($r,$selected,$self,$isoptionGroup);
				break;
		}
		
		if(++$i == count($rights)){
			
			$b = !empty($name);
			if($b && $self != $id){
				$Cu_tag = ($parentId==0 && $isoptionGroup) ? "optgroup" : "option" ; 
				if($selected==$id) {
					$name = ($parentId>0?repeat_string("-",count(explode(",",$catPath))-1):"").$name;
					$rtn_str .= '<'.$Cu_tag.' value="'.$id.'" label="'.$name.'" selected="selected">'.$name.'</a>';
				} else {
					$name = ($parentId>0?repeat_string("-",count(explode(",",$catPath))-1):"").$name;
					$rtn_str .= '<'.$Cu_tag.' value="'.$id.'" label="'.$name.'">'.$name.'</a>';
				}
			}
			if($submenu){
				$rtn_str .= /*($name ? '--' : '') .*/ $submenu ;//. ($name ? '--' : '');
			}
			if($b && $self != $id)
				$rtn_str .= '</'.$Cu_tag.'>';
				
			unset($Cu_tag);
		}
	}
	return $rtn_str;
}
function repeat_string($string="-",$loop){
	$newstring = '';
	for($i=1;$i<=$loop;$i++){
		$newstring .= $string;
	}
	return $newstring;
}    

/**
 * 获取一个键值对应的新闻分类
 */
function get_hash_newscategory() {
    $cates = get_data('newscategory');
    
    $data = array();
    foreach($cates as $key => $value) {
        if(is_array($value)) {
            foreach($value as $k => $v) {
                if(is_array($v)) {
                    foreach($v as $n => $m) {
                        $data[$k][$n] = $m;
                    }
                } else {
                    $data[$key][$k] = $v;
                }
            }
        }
    }
    return $data;
}


