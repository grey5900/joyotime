<?php
/**
 * 一些比较复杂的sql，方便以后修改
 * Create by 2012-4-1
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
$lang['sum_vest_all_unread_msg'] = 'select sum('.
	'(select count(*) from UserPrivateMessage where receiver=v.uid and isRead=0) + './/私信数量统计
	'(select count(*) from ReplyMessage where uid=v.uid and isRead=0)'.//回复提醒统计
	') as all_count from MorrisVest v, User u where u.id = v.uid AND v.aid=?';
//我的马甲显示消息列表时会用到
$lang['vest_msg_list_from_count'] = "(select upm.sender as suid, 
               upm.receiver as ruid, 
               max(upm.id) as maxId ,
              1 as type 
         from UserPrivateMessage upm, 
              MorrisVest v 
        where upm.receiver = v.uid 
          and v.aid = ? 
        group by suid,ruid 
        union all
       select pr.uid as suid,
              prm.uid as ruid,
              max(prm.id) as maxId ,
              2 as type 
        from Reply pr,
             ReplyMessage prm,
             MorrisVest v 
       where pr.id = prm.replyId 
         and prm.uid = v.uid 
         and v.aid= ?
       group by ruid 
       ) AS tmp"; 
$lang['vest_msg_list_from'] = /*'('.
	//查询私信
	'select upm.sender as suid, upm.receiver as ruid, max(upm.createDate) as createDate,'.
	'(select isRead from UserPrivateMessage where receiver=ru.id and sender=su.id order by createDate desc limit 1) as isRead,'.
	'if(su.nickname is not null and su.nickname != \'\',su.nickname,su.username) as s_name,'.
	'if(ru.nickname is not null and ru.nickname != \'\',ru.nickname,ru.username) as r_name,'.
	'1 as type '.
	'from UserPrivateMessage upm, User su, User ru, MorrisVest v '.
	'where su.id=upm.sender and ru.id=upm.receiver and upm.receiver=v.uid and v.aid=? '.
	'group by suid,ruid '.
	'union '.
	//查询回复
	'select pr.uid as suid,prm.uid as ruid,max(pr.createDate) as createDate,'.
	'(select isRead from ReplyMessage where uid=prm.uid order by id desc limit 1) as isRead,'.
	'\'回复\' as s_name,'.
	'if(u.nickname is not null and u.nickname != \'\',u.nickname,u.username) as r_name,'.
	'2 as type '.
	'from Reply pr,User u, ReplyMessage prm,MorrisVest v '.
	'where u.id = prm.uid and pr.id = prm.replyId and prm.uid = v.uid and v.aid=? '.
	'group by ruid order by createDate desc'.
	') AS tmp'*/
	"SELECT tmp.suid , tmp.ruid ,       
 if( tmp.type = 1 , m.createDate , n.createDate ) as createDate ,   
     if( tmp.type = 1 , m.isRead , n.isRead ) as isRead  ,     
   if(su.nickname is not null and su.nickname != '',su.nickname,su.username) as s_name,  
      if(ru.nickname is not null and ru.nickname != '',ru.nickname,ru.username) as r_name,  
      tmp.type   FROM ( (select upm.sender as suid,         
        upm.receiver as ruid,       
          max(upm.id) as maxId ,      
         1 as type       
    from UserPrivateMessage upm,     
           MorrisVest v    
      where upm.receiver = v.uid  
          and v.aid = ? 
   group by suid,ruid      
    order by maxId desc          )  
       union all   
     ( select pr.uid as suid,     
          prm.uid as ruid,        
       max(prm.id) as maxId ,     
          2 as type     
   from Reply pr,         
     ReplyMessage prm,    
          MorrisVest v    
     where pr.id = prm.replyId      
     and prm.uid = v.uid         
  and v.aid= ? 
    group by ruid      
  order by maxId desc   
       )      
  ) AS tmp   
    left join UserPrivateMessage m   
      on tmp.type = 1 and tmp.maxId = m.id    
    left join ReplyMessage n    
     on tmp.type = 2 and tmp.maxId = n.id   
    left join User su      
   on tmp.suid = su.id   
    left join User ru      
   on tmp.ruid = ru.id  
 "
	;
//我的马甲查询消息列表总长度时会用到
$lang['vest_msg_list_count_from'] = '(SELECT upm.id,upm.content, upm.isRead,upm.createDate AS created, upm.sender AS fromId,upm.receiver AS toId, u.nickname AS sName,(SELECT nickname FROM User WHERE id=v.uid) AS rName, 1 AS mtype
	FROM UserPrivateMessage upm, User u, MorrisVest v WHERE upm.sender=u.id AND upm.receiver=v.uid AND v.aid=? GROUP BY fromId, toId UNION SELECT sm.id, sm.content, sm.isRead, sm.createDate AS created, 
	IF(sm.type=0,0,sm.itemId) AS fromId,sm.recieverId AS toId, \'系统\' AS sName,u.nickname AS rName, 2 AS mtype FROM SystemMessage sm, User u, MorrisVest v WHERE sm.recieverId=v.uid AND u.id=v.uid AND v.aid=? GROUP BY fromId, toId) AS tmp';

/*$lang['vest_reply_list_from'] = 'SELECT pr.*,if(s.nickname is not null and s.nickname != \'\',s.nickname,s.username) as s_name,'.
	'pl.placename,p.placeId,p.uid as owner,p.type as ptype,s.avatar '.
	'FROM ReplyMessage prm '.
	'INNER JOIN Reply pr ON pr.id=prm.replyId '.
	'INNER JOIN Post p ON p.id=pr.itemId '.
	'INNER JOIN User s ON s.id=pr.uid '.
	'LEFT JOIN Place pl ON pl.id=p.placeId '.
	'WHERE prm.uid=? '.
	'AND p.type != 1 '.
	'AND pr.uid != prm.uid '.
	'ORDER BY pr.createDate DESC LIMIT ?,?';*/

$lang['vest_reply_list_from'] = "SELECT pr.*,if(s.nickname is not null and s.nickname != '',s.nickname,s.username) as s_name,
	s.avatar ,prm.isread
	FROM ReplyMessage prm 
	INNER JOIN Reply pr ON pr.id=prm.replyId 
	INNER JOIN User s ON s.id=pr.uid 
	WHERE prm.uid=?
	AND pr.uid != prm.uid 
	ORDER BY pr.createDate DESC LIMIT ?,? ";

 
// File end