package com.joyotime.xr87.job.device;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

import org.springframework.dao.DataAccessException;
import org.springframework.transaction.TransactionStatus;
import org.springframework.transaction.support.TransactionCallbackWithoutResult;

import com.google.gson.Gson;
import com.joyotime.xr87.job.AbstractJob;
import com.joyotime.xr87.job.JobException;
import com.joyotime.xr87.job.JobStatus;
import com.joyotime.xr87.job.util.BaseInfo;
import com.joyotime.xr87.util.Tools;

/**
 * 统计设备的任务
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-20
 */

public class JobDevice extends AbstractJob {
	// 渠道ID对应渠道商ID号
	private Map<String, String> channelInfo;

	// 几个需要替换变量的SQL
	private String sqlNewUserCount;
	private String sqlNewCount;
	private String sqlConnectCount;
	private String sqlActiveCount;

	private String whereSql;

	// 用户保存读取的accessLOG统计的数据
	List<Integer> accessCount = null;
	
	// 数据保存的目录
	private String dataPath;

	private int sleepTime;
	private String splitChar = "-";
	
	public JobDevice() {
		dataPath = Tools.root + BaseInfo.config.get("dataPath");
		sleepTime = Tools.parseInt(BaseInfo.config.get("sleepTime"));
	}

	@SuppressWarnings("unchecked")
	@Override
	public boolean execute() {
		// 开始日期
		String startDate;
		// SQL变量
		String sql;
		// 结果集Map
		Map<String, Object> map;
		// 处理json的对象
		Gson gson = new Gson();
		// 日志Map
		Map<String, String> logMap = new TreeMap<String, String>();
		// 默认日志的ID，从1开始
		int logId = 1;
		try {
			String logStr = readLog();
			logStr = logStr.trim();
			logMap = (Map<String, String>) gson.fromJson(logStr, Map.class);

			startDate = logMap.get("statDate");
			logId = Tools.parseInt(logMap.get("logId"));
		} catch (JobException e) {
			// 如果读取异常
			// 去数据库读取最后一次统计的时间
			sql = "SELECT statDate FROM StatUserDevice ORDER BY statDate DESC LIMIT 1";
			try {
				map = jdbcTemplate.queryForMap(sql);
			} catch (DataAccessException ex) {
				map = null;
				new JobException("获取数据最后一次统计时间出错", e);
			}
			if (null == map) {
				// 没有记录
				// 去获取第一条ConnectLog日志记录
				sql = "SELECT createDate FROM ConnectLog ORDER BY createDate ASC LIMIT 1";
				try {
					map = jdbcTemplate.queryForMap(sql);
					startDate = ("" + map.get("createDate")).substring(0, 10);
				} catch (DataAccessException ex) {
					startDate = "2011-03-22";
				}
			} else {
				startDate = "" + map.get("statDate");
			}
			logMap.put("status", JobStatus.START.toString());
			logMap.put("statDate", startDate);

			// 去初始化获取logId
			sql = "SELECT id FROM ConnectLog WHERE createDate < '" + startDate
					+ " 23:59:59' ORDER BY createDate DESC LIMIT 1";
			try {
				map = jdbcTemplate.queryForMap(sql);
				logId = Tools.parseInt(String.valueOf(map.get("id")));
			} catch (DataAccessException ex) {
				logId = 1;
			}

			logMap.put("logId", String.valueOf(logId));
		}
		if (JobStatus.STOP.toString().equals(logMap.get("status"))) {
			// 如果设置为停止
			return true;
		}
		// 初始化渠道对应Map
		// 每次执行任务的时候初始化，这样子可以去刷最新的渠道
		initChannel();

		// 先统计ConnectLog日志，加入设备统计表
		// 选出最大的ConnectLog的ID号
		int maxLogId = jdbcTemplate
				.queryForInt("SELECT ifnull(max(id), 1) FROM ConnectLog");
		while (logId <= maxLogId) {
			try {
				map = jdbcTemplate
						.queryForMap("SELECT * FROM ConnectLog WHERE id = '"
								+ logId + "'");
			} catch (DataAccessException e) {
				// 没有取到记录。但是需要记录这个ID。并进行一个ID
				// 写入日志
				try {
					logMap.put("logId", "" + logId++);
					writeLog(gson.toJson(logMap, Map.class));
				} catch (JobException ex) {
				}
				continue;
			}

			String deviceCode = String.valueOf(map.get("deviceCode")).trim();
			deviceCode = "null".equals(deviceCode)?"":deviceCode;
			
			// 如果是android截取前面3端作为设备号
			if("1".equals(String.valueOf(map.get("deviceType"))) && !"".equals(deviceCode)) {
				String[] codes = deviceCode.split(splitChar);
				int codeLen = codes.length;
				if(codeLen > 3) {
					StringBuffer sb = new StringBuffer();
					String s = "";
					for(int i=0; i<3; i++) {
						sb.append(s);
						sb.append(codes[i]);
						s = splitChar;
					}
					
					deviceCode = sb.toString();
				}
			}
			
			if (!("".equals(deviceCode))) {
				// 判断是否已经存在StatDeviceInfo表中
				int num = jdbcTemplate
						.queryForInt("SELECT COUNT(*) AS num FROM StatDeviceInfo WHERE deviceCode = '"
								+ deviceCode + "'");
				if (num > 0) {
					// 已经存在
					jdbcTemplate
							.update("UPDATE StatDeviceInfo SET lastDate='"
									+ map.get("createDate")
									+ "', connectCount = connectCount + 1 WHERE deviceCode = '"
									+ deviceCode + "'");
				} else {
					// 不存在
					Map<String, Object> data = new TreeMap<String, Object>();
					// 设备号
					data.put("deviceCode", deviceCode);
					// 设备类型
					data.put("deviceType", map.get("deviceType"));
					// 第一次时间
					data.put("createDate", map.get("createDate"));
					// 最后一次时间，默认第一次时间
					data.put("lastDate", map.get("createDate"));
					// 连接次数
					data.put("connectCount", 1);
					// 获取IP
					String ip = String.valueOf(map.get("ip"));
					if (null == map.get("ip") || "".equals(ip)) {
						// 如果为null或者为空
						// 去用户表获取用户的IP
						try {
							Map<String, Object> userMap = jdbcTemplate
									.queryForMap("SELECT * FROM User WHERE deviceCode = '"
											+ deviceCode
											+ "' ORDER BY id ASC LIMIT 1");
							ip = String.valueOf(userMap.get("lastIp"));
						} catch (DataAccessException e) {
							// 如果没有默认为127.0.0.1
							ip = "127.0.0.1";
						}
					}

					data.put("ip", ip);
					// 获取IP信息
					if (null != ip && !("".equals(ip)) && !("null".equals(ip))) {
						// 如果IP不是NULL也不是空，也不是字符串null
						Map<String, String> area = getArea(ip);
						data.put("ipArea", area.get("name"));
						data.put("province", area.get("province"));
						data.put("city", area.get("city"));
					} else {
						data.put("ipArea", "");
						data.put("province", "");
						data.put("city", "");
					}

					// 去获取渠道及渠道商
					String channelId = "" + map.get("channelId");
					String parentId = channelInfo.get(channelId);
					if (null == parentId) {
						// 如果没有获取到渠道信息，那么去数据库获取
						try {
							Map<String, Object> merchantMap = jdbcTemplate
									.queryForMap("SELECT * FROM ChannelInfo WHERE id = '"
											+ channelId + "' LIMIT 1");
							parentId = "" + merchantMap.get("parentId");
							data.put("channelId", channelId);
							data.put("merchantId", parentId);
							// 写入channelInfo的Map，便于以后查询
							channelInfo.put(channelId, parentId);
						} catch (DataAccessException e) {
							data.put("channelId", 0);
							data.put("merchantId", 0);
						}
					} else {
						data.put("channelId", channelId);
						data.put("merchantId", parentId);
					}

					// 加入StatDeviceInfo表
					try {
						jdbcTemplate.execute(Tools.generatorSql("StatDeviceInfo",
								data, "INSERT"));
					} catch(DataAccessException e) {
						new JobException(e);
					}
				}
			}

			// 写入日志
			try {
				logMap.put("logId", "" + logId++);
				writeLog(gson.toJson(logMap, Map.class));
			} catch (JobException e) {
			}
		}

		// 获取当前日期
		String nowDate = Tools.getNow(1);
		// 去获取执行日志的最后一次执行的时间
		Date endDate = Tools.format(nowDate, 1);
		Date logDate = Tools.format(startDate, 1);
		
		final Set<String> channelIds = channelInfo.keySet();
		// 开始统计
		dayStat: while (true) {
			// 判断记录的日志时间和当前时间是否相同或者已经过了
			if (Tools.compareTime(logDate, endDate) >= 0) {
				break;
			}
			final String date = Tools.format(logDate, 1);

			// 读取计数，10次未读取到accessLog的数据文件，那么跳出统计
			int i = 0;
			readLogFile: while (true) {
				File f = new File(dataPath + date);
				if (f.exists()) {
					accessCount = new ArrayList<Integer>();
					// 文件存在，那么读取里面的数据
					BufferedReader br = null;
					try {
						br = new BufferedReader(new FileReader(f));
						// 读取第一行
						String line = "";
						while ((line = br.readLine()) != null) {
							String[] strs = line.split("\t");
							for (String str : strs) {
								accessCount.add(Tools.parseInt(str));
							}
						}
					} catch (Exception e) {
						// 读文件出错了
						new JobException("读取AccessLog文件出错[" + i + "]", e);
					} finally {
						if (null != br) {
							try {
								br.close();
							} catch (Exception e) {
							}
						}
					}
					if (accessCount.size() < 9) {
						// 读取出错或者数据不完整
						new JobException("错误的AccessLog统计数据", null);
					} else {
						// 读取成功退出读取文件循环
						break readLogFile;
					}
				}
				if (i++ == 9) {
					// 10次读取AccessLog都出错了，那么跳出统计循环，本次任务结束
					new JobException("读取AccessLog统计文件出错", null);
					break dayStat;
				}
				// 如果文件没有读取到，那么等10分钟再去读取
				Tools.sleep(600000L);
			}

			// 0点开始时间
			final String t1 = date + " 00:00:00";
			// 24点结束时间
			final String t2 = date + " 23:59:59";
			whereSql = "x.createDate BETWEEN '%s' AND '%s'";

			// 7天前
			final String t7 = Tools.format(
					Tools.dateAdd(Tools.format(t1, -1), -7, 4), -1);
			// 15天前
			final String t15 = Tools.format(
					Tools.dateAdd(Tools.format(t1, -1), -15, 4), -1);

			// 统计每天的数据
			Map<String, Object> statMap = new HashMap<String, Object>();
			statMap.put("statDate", date);
			// 统计累积到这天的用户包括网站注册
			statMap.put(
					"userCount",
					jdbcTemplate.queryForInt(String
							.format("SELECT COUNT(*) AS num FROM User WHERE createDate <= '%s'",
									t2)));
			// 统计新增用户数
			statMap.put(
					"newUserCount",
					jdbcTemplate.queryForInt(String
							.format("SELECT COUNT(*) AS num FROM User WHERE createDate BETWEEN '%s' AND '%s'",
									t1, t2)));
			// 统计新设备数
			sqlNewCount = "SELECT COUNT(*) AS num FROM StatDeviceInfo x WHERE "
					+ whereSql;
			statMap.put("newCount", jdbcTemplate.queryForInt(String.format(
					sqlNewCount, t1, t2)));
			// 统计连接数
			sqlConnectCount = "SELECT COUNT(*) AS num FROM ConnectLog x WHERE "
					+ whereSql;
			statMap.put("connectCount", jdbcTemplate.queryForInt(String.format(
					sqlConnectCount, t1, t2)));
			// 统计活跃数
			statMap.put("activeCount", accessCount.get(0) + accessCount.get(1)
					+ accessCount.get(2));
			// 统计7日活跃数
			statMap.put("activeCount7", accessCount.get(3) + accessCount.get(4)
					+ accessCount.get(5));
			// 统计15日活跃数
			statMap.put(
					"activeCount15",
					accessCount.get(6) + accessCount.get(7)
							+ accessCount.get(8));
			// 添加数据到用户设备每日统计表
			jdbcTemplate.execute(Tools.generatorSql("StatUserDeviceDay",
					statMap, "REPLACE"));

			// 加入设备类型条件
			// 新活跃数统计（用于统计不同渠道商）
			sqlActiveCount = "SELECT COUNT(DISTINCT(deviceCode)) AS num FROM ConnectLog x WHERE "
					+ whereSql + " AND x.deviceType = '%s'";
			// 新用户
			sqlNewUserCount = "SELECT COUNT(*) AS num FROM User x "
					+ "INNER JOIN StatDeviceInfo sd ON SUBSTRING_INDEX(x.deviceCode, '-', 3) = sd.deviceCode WHERE "
					+ whereSql + " AND sd.deviceType = '%s'";
			// 连接数
			sqlConnectCount += " AND x.deviceType = '%s'";
			// 新设备
			sqlNewCount += " AND x.deviceType = '%s'";

			// 用户保存统计ID
			final List<String> statId = new ArrayList<String>();
			try {
				// 加入事务
				tt.execute(new TransactionCallbackWithoutResult() {
					protected void doInTransactionWithoutResult(
							TransactionStatus status) {
						// 统计不同的平台 0：ios 1：android 2：wp
						for (int deviceType = 0; deviceType < 3; deviceType++) {
							Map<String, Object> dataMap = new TreeMap<String, Object>();
							dataMap.put("statDate", date);
							dataMap.put("deviceType", deviceType);
							// 统计新用户数（用户表User和设备信息表StatDeviceInfo设备号相同的记录统计）
							dataMap.put("newUserCount", jdbcTemplate
									.queryForInt(String.format(sqlNewUserCount,
											t1, t2, deviceType)));

							// 统计连接数（启动次数，只需要查询连接日志ConnectLog）
							dataMap.put("connectCount", jdbcTemplate
									.queryForInt(String.format(sqlConnectCount,
											t1, t2, deviceType)));

							// 统计新设备数
							dataMap.put("newCount", jdbcTemplate
									.queryForInt(String.format(sqlNewCount, t1,
											t2, deviceType)));

							// 活跃设备数 用日志文件数据
							dataMap.put("activeCount",
									accessCount.get(deviceType));

							// 7日活跃设备数
							dataMap.put("activeCount7",
									accessCount.get(deviceType + 3));

							// 15日活跃设备数
							dataMap.put("activeCount15",
									accessCount.get(deviceType + 6));

							Map<String, Object> map = null;
							try {
								map = jdbcTemplate
										.queryForMap("SELECT * FROM StatUserDevice WHERE statDate='"
												+ date
												+ "' AND deviceType='"
												+ deviceType + "'");
							} catch (DataAccessException e) {
								map = null;
							}

							String id = "";
							if (null == map) {
								// 新加入
								id = ""
										+ jdbcTemplate.insertAndGetKey(Tools
												.generatorSql("StatUserDevice",
														dataMap, "INSERT"));
							} else {
								// 更新
								id = "" + map.get("id");
								dataMap.put("id", id);
								jdbcTemplate.execute(Tools.generatorSql(
										"StatUserDevice", dataMap, "REPLACE"));
							}

							statId.add(id);
						}
					}
				});
			} catch (Exception e) {
				// 执行添加统计用户设备表事务出错
				new JobException("执行添加统计用户设备表事务出错", e);
			}

			if (statId.size() == 3) {
				// 3个平台的ID都添加并获取成功，那么执行查询渠道的详细数据
				try {
					tt.execute(new TransactionCallbackWithoutResult() {
						protected void doInTransactionWithoutResult(
								TransactionStatus status) {
							for (int deviceType = 0; deviceType < 3; deviceType++) {
								String id = statId.get(deviceType);
								// 分三个平台统计
								for (String channelId : channelIds) {
									// 按渠道统计
									Map<String, Object> dataMap = new TreeMap<String, Object>();
									String parentId = channelInfo.get(channelId);
									dataMap.put("id", id);
									dataMap.put("channelId", channelId);
									dataMap.put("merchantId", parentId);

									// 统计新用户数（用户表User和设备信息表StatDeviceInfo设备号相同的记录统计）
									dataMap.put(
											"newUserCount",
											jdbcTemplate.queryForInt(String
													.format(sqlNewUserCount
															+ " AND x.channelId = '%s'",
															t1, t2, deviceType,
															channelId)));

									// 统计连接数（启动次数，只需要查询连接日志ConnectLog）
									dataMap.put(
											"connectCount",
											jdbcTemplate.queryForInt(String
													.format(sqlConnectCount
															+ " AND x.channelId = '%s'",
															t1, t2, deviceType,
															channelId)));

									// 统计新设备数
									dataMap.put(
											"newCount",
											jdbcTemplate.queryForInt(String
													.format(sqlNewCount
															+ " AND x.channelId = '%s'",
															t1, t2, deviceType,
															channelId)));

									// 活跃设备数（今日的连接数，去重减去新的设备数）
									dataMap.put(
											"activeCount",
											jdbcTemplate.queryForInt(String
													.format(sqlActiveCount
															+ " AND x.channelId = '%s'",
															t1, t2, deviceType,
															channelId)));

									// 7日活跃设备数
									dataMap.put(
											"activeCount7",
											jdbcTemplate.queryForInt(String
													.format(sqlActiveCount
															+ " AND x.channelId = '%s'",
															t7, t2, deviceType,
															channelId)));

									// 15日活跃设备数
									dataMap.put(
											"activeCount15",
											jdbcTemplate.queryForInt(String
													.format(sqlActiveCount
															+ " AND x.channelId = '%s'",
															t15, t2,
															deviceType, channelId)));

									jdbcTemplate.execute(Tools.generatorSql(
											"StatUserDeviceChannel", dataMap,
											"REPLACE"));

									Tools.sleep(sleepTime);
								}
							}
						}
					});
				} catch (Exception e) {
					new JobException("统计计算渠道数据出错", e);
				}
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

	/**
	 * 得到地址的地区信息
	 * 
	 * @param ip
	 * @return Map值：name全部信息 province省 city市
	 */
	private Map<String, String> getArea(String ip) {
		String address = BaseInfo.ipSeeker.getAddress(ip);
		Map<String, String> area = new TreeMap<String, String>();
		area.put("name", address);
		area.put("province", "");
		area.put("city", "");

		// 获取空格之前的地点信息端
		if (address.length() > 0) {
			address = address.indexOf(" ") == -1 ? address : address.substring(
					0, address.indexOf(" "));
			for (String key : BaseInfo.district.keySet()) {
				if (address.indexOf(key) == 0
						|| (address + "省").indexOf(key) == 0
						|| (address + "市").indexOf(key) == 0
						|| (address + "区").indexOf(key) == 0) {
					area.put("province", key);
					if (address.length() > key.length()) {
						List<String> list = BaseInfo.district.get(key);
						for (String val : list) {
							if (address.indexOf(key + val) == 0
									|| address.indexOf(key + "省" + val) == 0
									|| address.indexOf(key + "市" + val) == 0
									|| address.indexOf(key + "区" + val) == 0) {
								area.put("city", val);
								break;
							}
						}
					}

					break;
				}
			}
		}

		return area;
	}

	/**
	 * 初始化渠道对应渠道商ID的Map
	 */
	private void initChannel() {
		// 去获取渠道ID及渠道商ID
		channelInfo = new TreeMap<String, String>();
		try {
			List<Map<String, Object>> list = jdbcTemplate
					.queryForList("SELECT * FROM ChannelInfo WHERE parentId > 0 ORDER BY id ASC");
			for (Map<String, Object> row : list) {
				channelInfo.put("" + row.get("id"), "" + row.get("parentId"));
			}
		} catch (DataAccessException e) {
			new JobException("初始化渠道对应ID的Map是报错", e);
		}
	}
}
