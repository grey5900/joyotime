<?php
/**
  * css js组合
  * 
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-15
  */

// 得到扩展名
function get_extend($file_name) {
    $extend = explode(".", $file_name);
    $va = count($extend) - 1;
    return $extend[$va];
}

// 用来保存所有的文件内容
$files = array();

// 处理的文件
$header = array(
    'js' => 'Content-Type: application/x-javascript',
    'css' => 'Content-Type: text/css'
);

// query_string组成 最后一个是版本号
// style.css&event.css&1.0 或者 style.js&event.js&1.0
$query_string = $_SERVER['QUERY_STRING'];
parse_str($query_string, $strs);

$type = '';
foreach($strs as $k => $v) {
    // 判断是否为static里面的文件
    if(strpos($k, 'static/') !== 0) {
        continue;
    }
    $k = preg_replace(array('/_min_(js|css)$/', '/jquery_/', '/_(js|css)$/'), array('.min.$1', 'jquery.', '.$1'), $k);
    if(empty($type)) {
        $type = get_extend($k);
        empty($header[$type]) && die();
    }
    
    $f = '../' . $k;
    if(file_exists($f)) {
        $files[] = file_get_contents($f);
    }
}

header("Expires: " . date("D, j M Y H:i:s", strtotime("now + 10 years")) . " GMT");
header($header[$type]);

echo join("\n", $files);
