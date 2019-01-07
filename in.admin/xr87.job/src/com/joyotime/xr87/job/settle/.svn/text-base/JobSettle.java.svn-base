package com.joyotime.xr87.job.settle;

import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import org.springframework.dao.DataAccessException;
import org.springframework.transaction.TransactionException;
import org.springframework.transaction.TransactionStatus;
import org.springframework.transaction.support.TransactionCallbackWithoutResult;

import com.google.gson.Gson;
import com.joyotime.xr87.job.AbstractJob;
import com.joyotime.xr87.job.JobException;
import com.joyotime.xr87.job.JobStatus;
import com.joyotime.xr87.util.Tools;

/**
 * 结算任务
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-17
 */

public class JobSettle extends AbstractJob {
	private HashMap<String, Object> data0;
	private HashMap<String, Object> data1;

	@SuppressWarnings("unchecked")
	@Override
	public boolean execute() {
		String startDate;
		String sql;
		Map<String, Object> map;
		Gson gson = new Gson();
		Map<String, String> logMap = new HashMap<String, String>();
		try {
			String logStr = readLog();
			logStr = logStr.trim();
			logMap = (Map<String, String>)gson.fromJson(logStr, Map.class);
			
			startDate = logMap.get("statDate");
		} catch (JobException e) {
			// 如果读取异常
			// 去数据库读取最后一次统计的时间
			sql = "SELECT statDate FROM StatDeal ORDER BY statDate DESC LIMIT 1";
			try {
				map = jdbcTemplate.queryForMap(sql);
			} catch (DataAccessException ex) {
				map = null;
				new JobException("获取数据最后一次统计时间出错", e);
			}
			if (null == map) {
				// 没有记录
				startDate = "2012-07-01";
			} else {
				startDate = "" + map.get("statDate");
			}
			logMap.put("status", JobStatus.START.toString());
			logMap.put("statDate", startDate);
		}
		
		if(JobStatus.STOP.toString().equals(logMap.get("status"))) {
			// 如果设置为停止
			return true;
		}
		
		// 获取当前日期
		String nowDate = Tools.getNow(1);
		// 去获取执行日志的最后一次执行的时间
		Date endDate = Tools.format(nowDate, 1);
		Date logDate = Tools.format(startDate, 1);
		// 开始统计
		while (true) {
			// 判断记录的日志时间和当前时间是否相同或者已经过了
			if (Tools.compareTime(logDate, endDate) >= 0) {
				break;
			}
			String date = Tools.format(logDate, 1);
			// WHERE条件
			String whereDate = "ifnull(payDate, createDate) between '" + date
					+ " 00:00:00' and '" + date + " 23:59:59'";
			// 各种统计
			// 团购
			data0 = new HashMap<String, Object>();
			data0.put("statDate", date);
			data0.put("type", 0);
			// 电影票
			data1 = new HashMap<String, Object>();
			data1.put("statDate", date);
			data1.put("type", 1);

			// 总订单数
			data0.put("orderCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 12"));
			data1.put("orderCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 13"));

			// 统计非0元订单
			data0.put("orderCount1", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 12 AND money > 0"));
			data1.put("orderCount1", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 13 AND money > 0"));

			// 统计未付款订单
			data0.put("nonPaymentCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 12 AND isPayed = 0"));
			data1.put("nonPaymentCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 13 AND isPayed = 0"));

			// 统计非0未付款订单
			data0.put(
					"nonPaymentCount1",
					jdbcTemplate
							.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
									+ whereDate
									+ " AND itemType = 12 AND isPayed = 0 AND money > 0"));
			data1.put(
					"nonPaymentCount1",
					jdbcTemplate
							.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
									+ whereDate
									+ " AND itemType = 13 AND isPayed = 0 AND money > 0"));

			// 统计已付款订单
			data0.put("paidCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 12 AND isPayed = 1"));
			data1.put("paidCount", jdbcTemplate
					.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 13 AND isPayed = 1"));

			// 统计非0已付款订单
			data0.put(
					"paidCount1",
					jdbcTemplate
							.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
									+ whereDate
									+ " AND itemType = 12 AND isPayed = 1 AND money > 0"));
			data1.put(
					"paidCount1",
					jdbcTemplate
							.queryForInt("SELECT COUNT(*) AS num FROM OrderInfo WHERE "
									+ whereDate
									+ " AND itemType = 13 AND isPayed = 1 AND money > 0"));

			// 统计售出数及金额 已付款的
			map = jdbcTemplate
					.queryForMap("SELECT IFNULL(SUM(quantity), 0) AS quantity, IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 12 AND isPayed = 1");
			data0.put("saleCount", map.get("quantity"));
			data0.put("dealAmount", map.get("money"));
			map = jdbcTemplate
					.queryForMap("SELECT IFNULL(SUM(quantity), 0) AS quantity, IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
							+ whereDate + " AND itemType = 13 AND isPayed = 1");
			data1.put("saleCount", map.get("quantity"));
			data1.put("dealAmount", map.get("money"));

			// 统计非0出售数 已付款的
			data0.put(
					"saleCount1",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(quantity), 0) AS quantity FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 12 AND isPayed = 1 AND money > 0",
									String.class));
			data1.put(
					"saleCount1",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(quantity), 0) AS quantity FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 13 AND isPayed = 1 AND money > 0",
									String.class));

			// payWay 0：支付宝 1：银联
			// FUCK 。哪个在数据库comment里面写的1：支付宝 2：银联。。。最后才发现不对

			// 统计支付宝金额 已付款的
			data0.put(
					"alipayAmount",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 12 AND isPayed = 1 AND payWay = 0",
									String.class));
			data1.put(
					"alipayAmount",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 13 AND isPayed = 1 AND payWay = 0",
									String.class));

			// 统计银联金额 已付款的
			data0.put(
					"chinapayAmount",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 12 AND isPayed = 1 AND payWay = 1",
									String.class));
			data1.put(
					"chinapayAmount",
					jdbcTemplate
							.queryForObject(
									"SELECT IFNULL(SUM(money), 0) AS money FROM OrderInfo WHERE "
											+ whereDate
											+ " AND itemType = 13 AND isPayed = 1 AND payWay = 1",
									String.class));

			// 写入数据库
			try {
				tt.execute(new TransactionCallbackWithoutResult() {
					protected void doInTransactionWithoutResult(
							TransactionStatus status) {
						Map<String, Object> map;
						try {
							map = jdbcTemplate
									.queryForMap("SELECT * FROM StatDeal WHERE statDate='"
											+ data0.get("statDate")
											+ "' AND type = 0");
						} catch (DataAccessException e) {
							map = null;
						}

						String sql = "";
						// 团购
						if (null == map) {
							// 新加入
							sql = Tools.generatorSql("StatDeal", data0,
									"INSERT");
						} else {
							// 更新
							data0.put("id", map.get("id"));
							sql = Tools.generatorSql("StatDeal", data0,
									"REPLACE");
						}
						jdbcTemplate.execute(sql);
						try {
							map = jdbcTemplate
									.queryForMap("SELECT * FROM StatDeal WHERE statDate='"
											+ data1.get("statDate")
											+ "' AND type = 1");
						} catch (DataAccessException e) {
							map = null;
						}
						sql = "";
						// 电影票
						if (null == map) {
							// 新加入
							sql = Tools.generatorSql("StatDeal", data1,
									"INSERT");
						} else {
							// 更新
							data1.put("id", map.get("id"));
							sql = Tools.generatorSql("StatDeal", data1,
									"REPLACE");
						}
						jdbcTemplate.execute(sql);
					}
				});
			} catch (TransactionException e) {
				new JobException("添加结算统计回滚", e);
			}

			// 把日志时间写入文件，并+1天
			try {
				logMap.put("statDate", Tools.format(logDate, 1));
				writeLog(gson.toJson(logMap, Map.class));
			} catch (JobException e) {
			}
			logDate = Tools.dateAdd(logDate, 1, 4);
		}

		return true;
	}
}
