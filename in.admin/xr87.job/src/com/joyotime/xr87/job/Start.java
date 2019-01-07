package com.joyotime.xr87.job;

import java.io.IOException;
import java.net.InetAddress;
import java.net.InetSocketAddress;
import java.net.ServerSocket;

import org.quartz.impl.StdScheduler;
import org.springframework.context.support.FileSystemXmlApplicationContext;

import com.joyotime.xr87.job.util.BaseInfo;
import com.joyotime.xr87.util.SpringUtil;
import com.joyotime.xr87.util.Tools;

/**
 * 启动
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-17
 */

public class Start {
	public Start() {
		// 启动一个监听端口，用来判断程序只能运行一个
		try {
			int port = Tools.parseInt(BaseInfo.config.get("serverPort"));

			ServerSocket serverSocket = new ServerSocket();
			serverSocket.bind(new InetSocketAddress(InetAddress
					.getByAddress(new byte[] { 127, 0, 0, 1 }), port));

			// 绑定成功，启动任务
			StdScheduler schedulerFactory = SpringUtil
					.getBean("schedulerFactory");
			schedulerFactory.start();

			while (true) {
				try {
					serverSocket.accept();
				} catch (IOException e) {
				}
			}
		} catch (Exception e) {
			String message = "启动失败，请检查是否已经启动过。";
			new JobException(message, e);
			System.out.println(message);
			SpringUtil.getApplicationContext().destroy();
		}
	}

	public static void main(String[] args) {
		new FileSystemXmlApplicationContext("/config/applicationContext.xml");
	}
}
