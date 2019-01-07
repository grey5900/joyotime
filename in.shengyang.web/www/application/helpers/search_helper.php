<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
  * 搜索的helper
  * 
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-12
  */

/**
 * 生成查询字符串
 * @param query $q mixed
 * @param 起始数 $start
 * @param 返回行数 $rows
 * @param filter query 查询不同类型的时候区分类型 
 *  类型
    10：楼盘
    20：地点
    30：用户
    40：POST数据
    50：新闻 
    $fq 可以为多个值及查询范围等 mixed
 * @param 排序 $sort mixed 格式：sort=<field name>+<desc|asc>[,<field name>+<desc|asc>]
 * @param $qf query fields 如：title^1 desc^0.1
 * @param $pf phrase fields 如：title desc
 * @param defType edismax $def_type
 * @param $bf boosts function
 * @param 返回数据类型 $wt
 */
function build_query_string($q, $start = 0, $rows = 10, $fq = '', 
        $sort = '', $qf = 'title^1 desc^0.1', $pf = 'title desc', 
        $def_type = 'edismax', $bf = '', $wt = 'json') {
    // 主要是上次写了一个函数了，不想去改了，就这样子用吧
    $query_string = search_query_string($q, $fq, $start, $rows, $wt);
    
    $sort && ($query_string .= sprintf('&sort=%s', urlencode($sort)));

    $qf && ($query_string .= sprintf('&qf=%s', urlencode($qf)));
    
    $pf = urlencode($pf);
    $pf && ($query_string .= sprintf('&pf=%s&hl=true&hl.fl=%s', $pf, $pf));
    
    $def_type && ($query_string .= sprintf('&defType=%s', $def_type));
    
    $bf && ($query_string .= sprintf('&bf=%s', $bf));
        
    return $query_string;
}

/**
 * 生成查询query_string
 * @param 查询 $q
 * @param 过滤 $fq
 * @param 开始记录 $start
 * @param 行数 $rows
 * @param 返回数据类型 $wt
 */
function search_query_string($q, $fq, $start = 0, $rows = 10, $wt = 'json') {
    $q = is_array($q)?implode('%20OR%20', $q):$q;
    empty($q) && $q = '*:*';
    $fq = is_array($fq)?implode('&fq=', $fq):$fq;
    empty($fq) || $fq = sprintf('&fq=%s', $fq);
    
    return sprintf('q=%s%s&start=%s&rows=%s&wt=%s', $q, $fq, $start, $rows, $wt);
}

/**
 * 处理价格区间
 * @param 价格 $price
 */
function price_range($price) {
    if(strpos($price, '以下') !== false) {
        return '[0 TO ' . floatval($price) . ']';
    } elseif(strpos($price, '以上') !== false) {
        return '[' . floatval($price) . ' TO *]';
    } else {
        list($price1, $price2) = explode('-', $price);
        return sprintf('[%s TO %s]', floatval($price1), floatval($price2));
    }
}

/**
 * 获取分页
 * @param 地址 $url 为地址的格式化字符串 如：http://www.joyotime.com/action/%s
 * @param 总数量 $total_num
 * @param 当前页 $page
 * @param 每页条数 $page_size
 * @param 显示页码数 $page_num
 * @return $pagination prev next pages=>url,page
 */
function pagination($url, $total_num, $page, $page_size, $page_num) {
    $pagination = array();
    if($page > 1) {
        // 当前页码大于1才有上一页
        $pagination['prev'] = sprintf($url, $page - 1);
    }
    // 总页码
    $max_page = ceil($total_num / $page_size);
    if($page < $max_page) {
        // 当前页码小于总页码才有下一页
        $pagination['next'] = sprintf($url, $page + 1);
    }
    
    if($page_num > 0 && $max_page > 0) {
        if($max_page <= $page_num) {
            // 如果总数小于等于要显示的页码。那就都显示
            $pages = range(1, $max_page);
        } else {
            // 计算中间要显示的页码
            $avg_page = intval(ceil($page_num/2));
            $start_page = ($page <= $avg_page)?1:($page - $avg_page + 1);
            $end_page = $start_page + $page_num - 1;
            ($end_page > $max_page) && $end_page = $max_page;
            $start_page = $end_page - $page_num + 1;
            $pages = range($start_page, $end_page);
        }
        
        if($pages) {
            foreach($pages as $p) {
                $pagination['pages'][$p] = array(
                        'url' => sprintf($url, $p),
                        'is_cur' => ($p == $page)
                        );
            }
        }
    }
    
    
    return $pagination;
}