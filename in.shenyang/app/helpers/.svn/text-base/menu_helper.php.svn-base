<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 生成menu函数
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-3-2
 */

/**
 * 生成菜单
 * @param $menus 所有的菜单
 * @param $rights 用户所拥有的权限
 * @return string
 */
function build_menu($menus, $rights) {
    $rtn_str = '';
    $name = '';
    $uri = '';
    $submenu = '';
    $id = '';
    $i = 0;
    foreach ($menus as $key => $value) {
        switch($key) {
            case 'id' :
                $id = $value;
                break;
            case 'name' :
                $name = $value;
                break;
            case 'uri' :
                $uri = $value;
                break;
            default :
                if (is_int($key)) {
                    $submenu .= build_menu($value, $rights);
                }
        } 
        
        if (++$i == count($menus)) {
            // 最后
            $b = $name && is_array($rights) && in_array($id, $rights);
            if ($b) {
                $rtn_str .= '<li>';
                if ('javascript:;' != $uri) {
                    $ext_a = ' target="navTab" rel="' . substr(strtr($uri, '/', '_'), 1) . '"';
                }
                $rtn_str .= '<a href="' . $uri . '"' . $ext_a . '>' . $name . '</a>';
            }
            if ($submenu) {
                $rtn_str .= ($name ? '<ul>' : '') . $submenu . ($name ? '</ul>' : '');
            }
            if ($b) {
                $rtn_str .= '</li>';
            }
        }
    }
    return $rtn_str;
}

/**
 * 生成带checkcbox的树形菜单
 * Create by 2012-3-8
 * @author liuw
 * @param array $rights
 */
function build_check_tree($rights){
	$rtn_str = $name = $uri = $id = $submenu = $checked = '';
	$i = 0;
	foreach($rights as $key=>$r){
		switch($key){
			case 'name':$name = $r;break;
			case 'id':$id = $r;break;
			case 'checked':$checked = $r;break;
			default:
				if(is_int($key))
					$submenu .= build_check_tree($r);
				break;
		}
		if(++$i == count($rights)){
			$b = !empty($name);
			if($b){
				if($checked)
					$rtn_str .= '<li><a tname="rids[]" tvalue="'.$id.'" checked="true">'.$name.'</a>';
				else 
					$rtn_str .= '<li><a tname="rids[]" tvalue="'.$id.'">'.$name.'</a>';
			}
			if($submenu){
				$rtn_str .= ($name ? '<ul>' : '') . $submenu . ($name ? '</ul>' : '');
			}
			if($b)
				$rtn_str .= '</li>';
		}
	}
	return $rtn_str;
}
