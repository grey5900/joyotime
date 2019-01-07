package com.joyotime.xr87.util;

import org.springframework.beans.BeansException;
import org.springframework.context.ApplicationContext;
import org.springframework.context.ApplicationContextAware;
import org.springframework.context.support.FileSystemXmlApplicationContext;

/**
 * Spring工具类
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-10
 */

public class SpringUtil implements ApplicationContextAware {
	private static ApplicationContext applicationContext;

	public void setApplicationContext(ApplicationContext context)
			throws BeansException {
		SpringUtil.applicationContext = context;
	}
	
	public static FileSystemXmlApplicationContext getApplicationContext() {
		return (FileSystemXmlApplicationContext) SpringUtil.applicationContext;
	}

	@SuppressWarnings("unchecked")
	public static <T> T getBean(String name) {
		return (T) SpringUtil.applicationContext.getBean(name);
	}

	@SuppressWarnings("unchecked")
	public static <T> T getBean(String name, Object... obj) {
		return (T) SpringUtil.applicationContext.getBean(name, obj);
	}
}
