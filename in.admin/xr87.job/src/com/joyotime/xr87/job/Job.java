package com.joyotime.xr87.job;

/**
 * 任务接口
 *
 *
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-17
 */

public interface Job {		
	/**
	 * 任务具体执行
	 * @return
	 */
	public boolean execute();
	
	/**
	 * 启动任务
	 * @return
	 */
	public boolean start();
	
	/**
	 * 任务停止
	 * @return
	 */
	public boolean stop(); 
	
	
}
