package com.joyotime.xr87.util;

import java.io.UnsupportedEncodingException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Map;
import java.util.StringTokenizer;

/**
 * 工具类
 * 
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-8-13
 */

public class Tools {
	public static final String fileSepa = System.getProperty("file.separator");
	public static final String root = System.getProperty("user.dir");

	/**
	 * 转换成整数
	 * 
	 * @param str
	 * @return
	 */
	public final static int parseInt(String str) {
		if (null == str || ("").equals(trim(str)))
			return 0;

		try {
			return Integer.parseInt(trim(str));
		} catch (RuntimeException e) {
			return 0;
		}
	}

	/**
	 * 切去字符串空格
	 * 
	 * @param str
	 * @return
	 */
	public final static String trim(String str) {
		return (null == str) ? "" : str.trim();
	}

	/**
	 * ISO-8859-1 转换成 GB2312
	 * 
	 * @param uni字符串
	 *            (ISO-8859-1编码)
	 * @return String
	 */
	public final static String uni2GB(String str) {
		return Tools.convertCoding(str, "ISO-8859-1", "GB18030");
	}

	/**
	 * GB2312 转换成 ISO-8859-1
	 * 
	 * @param gb字符串
	 *            (GBK编码)
	 * @return String
	 */
	public final static String gb2Uni(String str) {
		return Tools.convertCoding(str, "GB18030", "ISO-8859-1");
	}

	/**
	 * utf-8 转换成 GB2312
	 * 
	 * @param uni字符串
	 *            (ISO-8859-1编码)
	 * @return String
	 */
	public final static String utf2GB(String str) {
		return Tools.convertCoding(str, "UTF-8", "GB18030");
	}

	/**
	 * GB2312 转换成 utf-8
	 * 
	 * @param gb字符串
	 *            (GBK编码)
	 * @return String
	 */
	public final static String gb2Utf(String str) {
		return Tools.convertCoding(str, "GB18030", "UTF-8");
	}

	/**
	 * utf-8 转换成 ISO-8859-1
	 * 
	 * @param uni字符串
	 *            (ISO-8859-1编码)
	 * @return String
	 */
	public final static String utf2Uni(String str) {
		return Tools.convertCoding(str, "UTF-8", "ISO-8859-1");
	}

	/**
	 * ISO-8859-1 转换成 utf-8
	 * 
	 * @param gb字符串
	 *            (GBK编码)
	 * @return String
	 */
	public final static String uni2Utf(String str) {
		return Tools.convertCoding(str, "ISO-8859-1", "UTF-8");
	}

	/**
	 * 转换编码
	 * 
	 * @param str
	 *            字符串
	 * @param in
	 *            带入编码
	 * @param out
	 *            转换出的编码
	 * @return
	 */
	public final static String convertCoding(String str, String in, String out) {
		String rtn = "";
		if (null == str || ("").equals(str.trim()))
			return "";

		try {
			byte[] b = str.getBytes(in);
			rtn = new String(b, out);
		} catch (UnsupportedEncodingException e) {
			rtn = in;
		}

		return rtn;
	}

	/**
	 * 得到当前时间的不同表现形式
	 * 
	 * @param type
	 * @return
	 */
	public static final String getNow(int type) {
		return Tools.format(new Date(), type);
	}

	/**
	 * 格式化日期
	 * 
	 * @param time
	 * @param type
	 * @return
	 */
	public static final Date format(String time, int type) {
		SimpleDateFormat sdf = null;
		switch (type) {
		case 0:
			sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss.SSS");
			break;
		case 1:
			sdf = new SimpleDateFormat("yyyy-MM-dd");
			break;
		case 2:
			sdf = new SimpleDateFormat("yyyyMMdd");
			break;
		case 3:
			sdf = new SimpleDateFormat("yyyyMM");
			break;
		case 4:
			sdf = new SimpleDateFormat("yyyyMMddHHmmss");
			break;
		default:
			sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		}

		try {
			return sdf.parse(time);
		} catch (ParseException e) {
			return new Date();
		}
	}

	/**
	 * 格式化日期
	 * 
	 * @param time
	 *            时间戳
	 * @param type
	 * @return
	 */
	public static final String format(long time, int type) {
		return Tools.format(new Date(time), type);
	}

	/**
	 * 格式化日期
	 * 
	 * @param time
	 * @param type
	 * @return
	 */
	public static final String format(Date time, int type) {
		SimpleDateFormat sdf = null;
		switch (type) {
		case 0:
			sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss.SSS");
			break;
		case 1:
			sdf = new SimpleDateFormat("yyyy-MM-dd");
			break;
		case 2:
			sdf = new SimpleDateFormat("yyyyMMdd");
			break;
		case 3:
			sdf = new SimpleDateFormat("yyyyMM");
			break;
		case 4:
			sdf = new SimpleDateFormat("yyyyMMddHHmmss");
			break;
		default:
			sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		}

		return sdf.format(time);
	}

	/**
	 * 比较两个时间
	 * 
	 * @param start
	 * @param end
	 * @return 0:一样 1:d1时间晚 -1:d1时间早
	 */
	public static final int compareTime(Date d1, Date d2) {
		Calendar c1 = Calendar.getInstance();
		c1.setTime(d1);
		Calendar c2 = Calendar.getInstance();
		c2.setTime(d2);

		return c1.compareTo(c2);
	}

	/**
	 * 比较两个时间
	 * 
	 * @param d1
	 * @param t1
	 *            转换时间的类型
	 * @param d2
	 * @param t2
	 *            转换时间的类型
	 * @return 0:一样 1:d1时间晚 -1:d1时间早
	 */
	public static final int compareTime(String d1, int t1, String d2, int t2) {
		Calendar c1 = Calendar.getInstance();
		c1.setTime(format(d1, t1));
		Calendar c2 = Calendar.getInstance();
		c2.setTime(format(d2, t2));

		return c1.compareTo(c2);
	}

	/**
	 * 一个日期加上一个时间后
	 * 
	 * @param d1
	 * @param addnum
	 *            要加的时间
	 * @param type
	 *            加时间的类型 0: 毫秒 1：秒 2：分 3：小时 4：天 5：月 6：年
	 * @return
	 */
	public static final Date dateAdd(Date d1, int addnum, int type) {
		Calendar cal = Calendar.getInstance();
		cal.setTime(d1);

		switch (type) {
		case 0:
			cal.add(Calendar.MILLISECOND, addnum);
			break;
		case 1:
			cal.add(Calendar.SECOND, addnum);
			break;
		case 2:
			cal.add(Calendar.MINUTE, addnum);
			break;
		case 3:
			cal.add(Calendar.HOUR, addnum);
			break;
		case 4:
			cal.add(Calendar.DATE, addnum);
			break;
		case 5:
			cal.add(Calendar.MONTH, addnum);
			break;
		case 6:
			cal.add(Calendar.YEAR, addnum);
			break;
		}

		return cal.getTime();
	}

	/**
	 * 当前时间戳
	 * 
	 * @return
	 */
	public final static long timestamp() {
		return new Date().getTime();
	}

	/**
	 * 得到当前的unix时间戳
	 * 
	 * @return
	 */
	public final static int unixtimestamp() {
		return Tools.unixtimestamp(new Date().getTime());
	}

	/**
	 * 得到int的时间戳
	 * 
	 * @param time
	 * @return
	 */
	public final static int unixtimestamp(long time) {
		return new Long(time / 1000).intValue();
	}

	/**
	 * 把字符串的IP地址转换成JAVA中InetAddress需要的byte[]数据
	 * 
	 * @param ipAddress
	 * @return
	 */
	public final static byte[] getIpAsArrayOfByte(String ipAddress) {
		StringTokenizer st = new StringTokenizer(ipAddress, ".");
		byte[] ip = new byte[4];
		int i = 0;

		while (st.hasMoreTokens()) {
			ip[i++] = Byte.parseByte(st.nextToken());
		}

		return ip;
	}

	/**
	 * 生成SQL
	 * 
	 * @param tableName
	 * @param data
	 * @param type
	 *            INSERT REPLACE
	 * @return
	 */
	public final static String generatorSql(String tableName,
			Map<String, Object> data, String type) {
		StringBuffer sb = new StringBuffer();

		String delim0 = "", delim1 = "", fields = "", values = "";
		for (String key : data.keySet()) {
			fields += delim0 + key;
			values += delim1 + data.get(key);

			delim0 = ",";
			delim1 = "','";
		}

		type = "REPLACE".equals(type) ? "REPLACE" : "INSERT";
		sb.append(type);
		sb.append(" INTO ");
		sb.append(tableName);
		sb.append("(");
		sb.append(fields);
		sb.append(")");
		sb.append(" VALUES('");
		sb.append(values);
		sb.append("')");

		return sb.toString();
	}

	/**
	 * 用分隔符连接成一个字符串
	 * 
	 * @param ary
	 * @param delim
	 * @return
	 */
	public final static String implode(String[] ary, String delim) {
		String out = "";
		for (int i = 0; i < ary.length; i++) {
			if (i != 0) {
				out += delim;
			}
			out += ary[i];
		}
		return out;
	}
	
	/**
	 * 暂停毫秒数
	 */
	public final static void sleep(long t) {
		try {
			Thread.sleep(t);
		} catch (InterruptedException e) {
		}
	}
}
