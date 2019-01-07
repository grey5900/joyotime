<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 模板类，根据DISCUZ代码修改
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-07
 */

class Template {
    // 模版文件夹
    var $template_dir = 'template/';
    // 编译后文件的文件夹
    var $compiled_dir = 'compiled/';
    // 模板文件后缀
    var $suffix = 'html';
    // 是否自动更新改过的模版
    var $auto_update = true;
    // 模板组
    var $group;
    // 模板开头插入的字符串
    var $pre_str = '';
    // 模板结束插入字符串
    var $suf_str = '';
    // 模版中的数据
    var $_data = array();
    // 语言
    var $languages = array();

    /**
     * 构造函数 php
     * @param $options
     * $template_dir 模板文件夹 默认template/
     * $compiled_dir 编译文件夹 默认compiled/
     * $suffix 模板后缀 默认html
     * $auto_update 是否自动更新模板 默认true
     * $group 模板组
     * $pre_str 插入编译模板前的字符串
     * $suf_str 插入编译模板结束的字符串
     */
    function __construct($options = array ()) {
        $this->setOptions($options);
    }

    /**
     * 设置参数
     */
    function setOptions($options = array ()) {
        if ($options && is_array($options)) {
            foreach ($options as $key => $value)
                $this->$key = $value;
        }
    }

    /**
     * 设置变量
     *
     * @param mix $key
     * @param string $value
     */
    function assign($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $var => $val)
                if ($var) {
                    $this->_data[$var] = $val;
                }
        } else {
            if ($key) {
                $this->_data[$key] = $value;
            }
        }
    }

    /**
     * 输出内容
     *
     * @param string $tpl 模版文件 不包括后缀
     */
    function display($tpl, $group = '') {
        $this->fetch($tpl, $group, true);
    }

    /**
     * 得到模版内容
     *
     * @param string $tpl 模版文件 不包括后缀
     * @param boolean $show 是否输出内容
     */
    function fetch($tpl, $group = '', $show = false) {
        $tmp_file = $this->get_template($tpl, $group);

        extract($this->_data);
        ob_start();
        include ($tmp_file);
        $html = ob_get_contents();
        ob_end_clean();
        unset($this->_data);
        unset($this->languages);
        if ($show) {
			die($html);
        } else {
            return $html;
        }
    }

    /**
     * 得到编译后的文件
     *
     * @param string $tpl
     * @return
     */
    function get_template($tpl, $group = '') {
        $group && $this->group = $group;
        // 设置模板分组
        $tpl .= '.' . $this->suffix;
        // 模板添加后缀
        $tmp_file = $this->_get_compiled_tpl($tpl);
        // 得到编译后的路径
        if (file_exists($tmp_file)) {
            // 文件存在，判断是否更改过
            if ($this->auto_update && @filemtime($tmp_file) < @filemtime($this->_get_template_tpl($tpl))) {
                // 自动更新 且已经修改过文件
                $this->_parse_template($tpl);
            }
        } else {
            // 文件不存在，编译文件
            $this->_parse_template($tpl);
        }

        return $tmp_file;
    }

    /**
     * 解析模版文件并保存     *
     * @param string $tpl
     */

    function _parse_template($tpl) {
        $filename = $this->_get_template_tpl($tpl);
        $template = $this->_readfile($filename);
        if (false === $template)
            exit("don't read template file[$tpl]");

        //模板
        $template = preg_replace("/\<\!\-\-\{template\s+(.+?)\}\-\-\>/ies", "\$this->_readtemplate('\\1')", $template);
        //处理子页面中的代码
        $template = preg_replace("/\<\!\-\-\{template\s+(.+?)\}\-\-\>/ies", "\$this->_readtemplate('\\1')", $template);

        //开始处理
        //变量
        $var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
        $template = preg_replace("/(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/s", "\\1['\\2']", $template);
        $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
        $template = preg_replace("/$var_regexp/es", "\$this->_addquote('<?=\\1?>')", $template);
        $template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "\$this->_addquote('<?=\\1?>')", $template);

        // 语言
        $template = preg_replace("/\{lang\s+(\w+?)\}/ise", "\$this->_lang('\\1')", $template);

        //PHP代码
        $template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/ies", "\$this->_stripvtags('<?php \\1?>')", $template);
        $template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\s*\}[\n\r\t]*/ies", "\$this->_stripvtags('<?php echo \\1?>')", $template);

        //逻辑
        $template = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->_stripvtags('<?php } elseif(\\1) { ?>','')", $template);
        $template = preg_replace("/\{else\}/is", "<?php } else { ?>", $template);
        //循环
        for ($i = 0; $i < 6; $i++) {
            $template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "\$this->_stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<?php } } ?>')", $template);
            $template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "\$this->_stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')", $template);
            $template = preg_replace("/\{if\s+(.+?)\}(.+?)\{\/if\}/ies", "\$this->_stripvtags('<?php if(\\1) { ?>','\\2<?php } ?>')", $template);
        }
        //常量
        $template = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s", "<?=\\1?>", $template);

        //换行
        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

        $objfile = $this->_get_compiled_tpl($tpl);
        // 写入编译文件
        if (!$this->_writefile($objfile, $this->pre_str . $template . $this->suf_str)) {
            exit("can't write compile file'[$objfile]");
        }
    }

    /**
     * 返回编译后的文件地址
     */
    function _get_compiled_tpl($tpl) {
        return $this->compiled_dir . ($this->group ? $this->group . '_' : '') . str_replace('/', '_', $tpl) . '.php';
    }

    /**
     * 返回模板路径
     */
    function _get_template_tpl($tpl) {
        return $this->template_dir . ($this->group ? $this->group . '/' : '') . $tpl;
    }

    /**
     * 模板是否存在
     */
    function template_exists($tpl, $group = '') {
        $group && $this->group = $group;
        // 设置模板分组
        $tpl .= '.' . $this->suffix;
        // 模板添加后缀

        return file_exists($this->template_dir . ($this->group ? $this->group . '/' : '') . $tpl);
    }

    /**
     * 读取模板文件数据
     * @param $name 模板名称
     */
    function _readtemplate($name) {
        $tpl = $this->template_dir . (strpos($name, '/') !== false ? ($name) : ($this->group . '/' . $name)) . '.' . $this->suffix;
        return file_get_contents($tpl);
    }

    function _readfile($filename) {
        $content = '';
        if (function_exists('file_get_contents')) {
            @$content = file_get_contents($filename);
        } else {
            if (@$fp = fopen($filename, 'r')) {
                @$content = fread($fp, filesize($filename));
                @ fclose($fp);
            }
        }
        return $content;
    }

    /**
     * 写入文件
     *
     * @param 文件名 $filename
     * @param 写入数据 $writetext
     * @param 打开文件模式 $openmod
     * @return 写入是否成功
     */
    function _writefile($filename, $writetext) {
        if (@$fp = fopen($filename, 'w')) {
            flock($fp, 2);
            fwrite($fp, $writetext);
            fclose($fp);
            // 锁定操作也可以被 fclose()释放
            return true;
        } else {
            return false;
        }
    }

    /**
     * 处理quote
     */
    function _addquote($var) {
        return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
    }

    /**
     * 处理变量
     */
    function _stripvtags($expr, $statement = '') {
        $expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr . $statement;
    }

    /**
     * 语言处理
     */
    function _lang($k) {
        return empty($this->languages[$k]) ? $k : "<?=\$this->languages['$k']?>";
    }
	
	/**
	 * 设置语言
	 */
	function set_language($language) {
		/*
		因为CI在lang->load的时候已经做了array_merge处理，所以这里不要，如果模板类单独使用需要			
				if($this->languages) {
					$this->languages = array_merge($this->languages, $language);
				} else {
					$this->languages = $language;
				}
				*/
		$this->languages = $language;
	}

}
