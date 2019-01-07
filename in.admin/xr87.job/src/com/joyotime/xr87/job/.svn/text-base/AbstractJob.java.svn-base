package com.joyotime.xr87.job;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;

import javax.sql.DataSource;

import org.springframework.jdbc.datasource.DataSourceTransactionManager;
import org.springframework.transaction.support.TransactionTemplate;

import com.joyotime.xr87.util.JdbcTemplateAdapter;
import com.joyotime.xr87.util.Tools;

/**
 * JOB抽象类
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-17
 */

public abstract class AbstractJob implements Job {
	/**
	 * 默认JOB执行状态，默认为启动状态
	 */
	protected JobStatus status = JobStatus.START;
	
	protected JdbcTemplateAdapter jdbcTemplate;
	
	protected TransactionTemplate tt;
	
	/**
	 * 日志文件地址
	 */
	protected String logFile; 
	
	@Override
	public boolean start() {
		status = JobStatus.START;
		return true;
	}
	
	@Override
	public boolean stop() {
		status = JobStatus.STOP;
		return true;
	}
	
	public void setDataSource(DataSource dataSource) {
		jdbcTemplate = new JdbcTemplateAdapter(dataSource);
	}
	
	public void setTransactionManager(DataSourceTransactionManager transactionManager) {
		tt = new TransactionTemplate(transactionManager);
	}
	
	public void setLogFile(String logFile) {
		this.logFile = Tools.root + logFile;
	}
	
	/**
	 * @throws JobException 
	 * 写入日志内容
	 * @param logStr
	 * @throws  
	 */
	public synchronized void writeLog(String logStr) throws JobException {
		File file = new File(logFile);
		try {
			if(!file.exists()) {
				// 如果文件不存在，那么创建文件
				if(!file.createNewFile()) {
					// 创建文件失败
					throw new JobException("创建日志文件[" + logFile + "]失败", null);
				}
			}
			
			PrintWriter pw = new PrintWriter(new FileOutputStream(file));
			pw.print(logStr);
			pw.flush();
			pw.close();
		} catch(FileNotFoundException e) {
			throw new JobException("写入日志文件[" + logFile + "]出错", e);
		} catch (IOException e) {
			throw new JobException("写入日志文件[" + logFile + "]出错", e);
		}
	}
	
	/**
	 * 读取日志内容
	 * @return
	 * @throws JobException 
	 */
	public synchronized String readLog() throws JobException {
		File file = new File(logFile);
		StringBuffer logStr = new StringBuffer();
		try {
			BufferedReader br = new BufferedReader(new FileReader(file));
			
			String line = "";
			while(null != (line = br.readLine())) {
				logStr.append(line);
			}
			
			br.close();
		} catch(FileNotFoundException e) {
			throw new JobException("读取日志文件[" + logFile + "]出错", e);
		} catch (IOException e) {
			throw new JobException("读取日志文件[" + logFile + "]出错", e);
		}
		
		return logStr.toString();
	}
}
