<?php
namespace Utility\Arrays;

class Access {
    
    public static function gets(&$row = array(), $key, $default = '') {
        if(stristr($key, '.')) {
            $keys = explode('.', $key);
            $item = self::gets($row, array_shift($keys), $default);
            if($item == $default) return $default;
            if(count($keys) > 0) {
                return self::gets($item, implode('.', $keys), $default);
            }
        }
    	if(isset($row[$key])) {
    		return $row[$key];
    	}
    	return $default;
    }
}