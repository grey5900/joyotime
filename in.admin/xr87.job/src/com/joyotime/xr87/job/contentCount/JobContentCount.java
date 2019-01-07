/**
 * 
 */
package com.joyotime.xr87.job.contentCount;

import java.sql.Timestamp;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;
import java.util.TreeMap;

import org.springframework.dao.DataAccessException;

import com.google.gson.Gson;
import com.joyotime.xr87.job.AbstractJob;
import com.joyotime.xr87.job.JobException;
import com.joyotime.xr87.job.JobStatus;
import com.joyotime.xr87.job.util.BaseInfo;
import com.joyotime.xr87.util.Tools;

/**
 * 统计内容数据 
 * Create by 2012-9-12
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
public class JobContentCount extends AbstractJob {
	
	private int sleepTime;//线程休眠时间
	
	public JobContentCount(){
		sleepTime = Tools.parseInt(BaseInfo.config.get("sleepTime"));
	}

	/* (non-Javadoc)
	 * @see com.joyotime.xr87.job.Job#execute()
	 */
	@SuppressWarnings("unchecked")
	@Override
	public boolean execute() {
		//处理JSON的对象
		Gson gson = new Gson();
		//日志MAP
		Map<String, String> logMap = new TreeMap<String, String>();
		//数据集合
		Map<String, Object> datas;
		//SQL
		String sql;
		//上次更新的日期
		String lastUpdate;
		//获取上次更新的日期
		try{
			String logStr = readLog().trim();
			logMap = (Map<String, String>)gson.fromJson(logStr, Map.class);
			lastUpdate = logMap.get("lastUpdate");
		}catch(JobException e){
			//获取更新日期失败的话从数据库查询最近的统计时间
			sql = "SELECT createDate FROM StatContentCount ORDER BY createDate DESC LIMIT 1";
			try{
				datas = jdbcTemplate.queryForMap(sql);
			}catch(DataAccessException ex){
				datas = null;
				new JobException("获取内容统计数据的最近更新时间失败了", e);
			}
			if(null == datas){
				//获取最早的POST时间做为最后更新时间
				sql = "SELECT createDate FROM Post WHERE status <= 1 ORDER BY createDate ASC LIMIT 1";
				datas = jdbcTemplate.queryForMap(sql);
				lastUpdate = "" + Tools.format(new Date(((Timestamp)datas.get("createDate")).getTime()), 1);
			}else{
				lastUpdate = "" + datas.get("createDate");
			}
			//压日志堆栈
			logMap.put("status", JobStatus.START.toString());
			logMap.put("lastUpdate", lastUpdate);
		}
		
		//如果设置停止任务
		if(JobStatus.STOP.toString().equals(logMap.get("status")))
			return true;
		//当前该统计的时间，比现实时间少一天
		long nowMill = System.currentTimeMillis() - 24*3600000;
		Calendar c = Calendar.getInstance(Locale.CHINA);
		c.setTimeInMillis(nowMill);
		String nowUpdate = Tools.format(c.getTime(), 1);
		//计算应该从什么时候开始检查数据完整性
		Date dLastUpdate = Tools.format(lastUpdate, 1);
		c = Calendar.getInstance(Locale.CHINA);
		c.setTime(dLastUpdate);
		long lLastUpdate = c.getTimeInMillis();
		//计算时间差
		long ex = Math.abs(lLastUpdate - nowMill);
		int exDay = Double.valueOf(Math.ceil(ex/24/3600000)).intValue();
		
		if(exDay==0)//已经统计过了
			return true;
		else{
			for(int i=0;i<exDay;i++){
				datas = new HashMap<String, Object>();
				lLastUpdate += 24*3600000;
				c.setTimeInMillis(lLastUpdate);
				String date = Tools.format(c.getTime(), 1);
				datas.put("createDate", date);
				//统计的开始和结束时间
				String start = date+" 00:00:00";
				String end = date+" 23:59:59";
				//统计签到数
				try{
					//统计签到数
					Map<String, Object> query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS checkinCount FROM Post WHERE type=1 AND status<=1 AND createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("checkinCount", query.get("checkinCount"));
					//统计点评数
					query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS tipCount FROM Post WHERE type=2 AND status<=1 AND createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("tipCount", query.get("tipCount"));
					//统计图片数
					query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS photoCount FROM Post WHERE type=3 AND status<=1 AND createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("photoCount", query.get("photoCount"));
					//统计回复数
					query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS replyCount FROM PostReply WHERE status<=1 AND createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("replyCount", query.get("replyCount"));
					//统计私信数
					query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS UPMCount FROM UserPrivateMessage WHERE createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("UPMCount", query.get("UPMCount"));
					//统计分享数
					query = jdbcTemplate.queryForMap("SELECT COUNT(id) AS shareCount FROM Share WHERE createDate BETWEEN '"+start+"' AND '"+end+"'");
					datas.put("shareCount", query.get("shareCount"));
					//保存数据
					jdbcTemplate.execute(Tools.generatorSql("StatContentCount", datas, "INSERT"));
					Tools.sleep(sleepTime);
				}catch(DataAccessException dae){
					logMap = new TreeMap<String, String>();
					logMap.put("status", "ERROR");
					logMap.put("Err", "统计内容分析数据出错");
					logMap.put("Date", date);
					try {
						writeLog(gson.toJson(logMap, Map.class));
					} catch (JobException e) {}
				}
			}
		}
		
		//把更新时间写入日志
		try{
			logMap.put("lastUpdate", nowUpdate);
			this.writeLog(gson.toJson(logMap, Map.class));
		}catch(JobException e){}
		
		return true;
	}

}
