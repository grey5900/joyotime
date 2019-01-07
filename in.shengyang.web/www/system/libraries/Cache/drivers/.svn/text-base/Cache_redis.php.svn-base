<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * redis缓存操作
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-12
 */

class CI_Cache_redis extends CI_Driver {
	// redis对象
	private $_redis;
	// 配置
	protected $_redis_conf = array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'ttl' => 3600,
			'pconnect' => true
	);

	/**
	 * 获取
	 * @param string
	 * @return string
	*/
	public function get($id) {
		return $this->_redis->get($id);
	}

	/**
	 * 保存数据
	 * @param string
	 * @param string
	 * @param int
	 * @return bool
	 */
	public function save($id, $data, $ttl = 0) {
		($this->_redis_conf['ttl'] && empty($ttl)) && ($ttl = $this->_redis_conf['ttl']);
		return $this->_redis->set($id, $data, $ttl);
	}

	/**
	 * 清除 只是当前库 flushAll 所有的库
	 */
	public function clean() {
		return $this->_redis->flushDB();
	}

	/**
	 * 缓存状态
	 */
	public function cache_info($type = NULL) {
		return $this->_redis->info();
	}

	/**
	 * 删除
	 * @param mixed
	 * @return
	 */
	public function delete($id) {
		return $this->_redis->delete($id);
	}

	/**
	 * 初始化redis
	 */
	private function _setup_redis() {
		// 导入配置文件
		$CI =& get_instance();
		if ($CI->config->load('config_redis', TRUE, TRUE)) {
			// 如果配置文件存在，且为数组
			if (is_array($CI->config->config['config_redis'])) {
				$this->_redis_conf = NULL;

				foreach ($CI->config->config['config_redis'] as $name => $conf) {
					$this->_redis_conf[$name] = $conf;
				}
			}
		}

		// 实例化
		$this->_redis = new Redis();

		$host = $this->_redis_conf['host'];
		empty($host) && ($host = '127.0.0.1');

		$port = $this->_redis_conf['port'];
		empty($port) && ($port = 6379);

		try {
    		if($this->_redis_conf['pconnect']) {
    			@$this->_redis->pconnect($host, $port);
    		} else {
    			@$this->_redis->connect($host, $port);
    		}
		} catch(RedisException $e) {}
	}

	/**
	 * 获取metadata信息
	 * redis的get方法比较多，所以这里直接返回空数组
	 * @return array
	 */
	public function get_metadata($id) {
		return array();
	}

	/**
	 * 是否支持redis
	 * @return bool
	 */
	public function is_supported() {
		if(!extension_loaded('redis')) {
			log_message('error', 'Redis不存在');

			return FALSE;
		}

		$this->_setup_redis();
		return TRUE;
	}

	/**
	 * 使用redis里面其他函数的调用，
	 */
	public function __call($func, $params) {
		return @call_user_func_array(array($this->_redis, $func), $params);
	}
}
