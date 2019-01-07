package com.joyotime.xr87.job;

import java.util.Date;

import org.apache.log4j.Logger;

/**
 * 异常处理
 *
 *
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-19
 */

public class JobException extends Exception {
	private static final long serialVersionUID = 1L;
	private Logger logger = Logger.getLogger(JobException.class);
	
	public JobException(String message) {
		this(message, null);
	}
	
	public JobException(Throwable t) {
		this(null, t);
	}

	public JobException(String message, Throwable t) {
		super(message, t);
		// 写入日志
		logger.error("[" + new Date() + "]" + message, t);
	}
}
