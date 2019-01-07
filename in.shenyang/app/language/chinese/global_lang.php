<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 公用的语言文件
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-8
 */


$lang['title'] = 'IN沈阳管理后台';

$lang['login_title'] = '后台登陆';
$lang['username'] = '账号';
$lang['password'] = '密码';
$lang['valicode'] = '验证码';
$lang['login_btn'] = '登陆';
$lang['valicode_tip'] = '看不清楚？点击更换';
$lang['invalid_code'] = '验证码不正确';
$lang['no_login'] = '您的登录已过期又或您还没有登录';
$lang['no_rights'] = '没有该操作权限';
$lang['login_error'] = '请输入正确的用户名或密码';
$lang['modify_pass'] = '修改密码';
$lang['logout_tip'] = '您真的要退出系统？';
$lang['copyright'] = 'Copyright ©2014 in.jin95.com';
$lang['home'] = '主页';

//Add by Liuw
$lang['role_name_to_lang'] = '角色名的长度不能超过20个字符';
$lang['login_success'] = '登录成功，欢迎您再次使用机甲';
$lang['do_success'] = '操作已成功执行';
$lang['do_error'] = '操作失败了';
$lang['rights_add_success'] = '权限添加成功';
$lang['rights_del_success'] = '权限删除成功';
$lang['rights_edit_success'] = '权限修改成功';
$lang['rights_edit_not_chang'] = '请点击要修改权限的编辑按钮';
$lang['rights_edit_not_here'] = '选择的权限不存在或已被删除';
$lang['role_add_success'] = '角色添加成功，请为该角色分配权限';
$lang['role_name_to_repeat'] = '角色名重复了！';
$lang['role_del_success'] = '角色已成功删除';
$lang['account_has_not_start'] = '帐号尚未启用，请联系管理员启用该帐号';
$lang['account_add_success'] = '帐号添加成功';
$lang['account_name_to_repeat'] = '帐号登录名重复了';
$lang['account_tname_to_repeat'] = '帐号实名重复了';
$lang['account_edit_success'] = '帐号修改成功';
$lang['account_del_success'] = '帐号已成功删除';
$lang['account_has_stop'] = '帐号已停用';
$lang['modify_pass_success'] = '您的密码已成功修改，请重新登录';
$lang['account_has_not_here'] = '帐号不存在';
$lang['modify_old_pwd_error'] = '旧密码与当前帐号不符';
$lang['place_cate_name_to_repeat'] = '分类名重复了';
$lang['place_cate_add_success'] = '分类添加成功';
$lang['place_cate_edit_success'] = '分类修改成功';
$lang['place_cate_delete_success'] = '分类删除成功';
$lang['place_cate_level_error'] = '分类等级不正确，指定上级分类后新分类的等级不能为最高';

$lang['tip_cache_success'] = '缓存更新成功';

//Add by Liuw
$lang['post_type_error'] = '非法操作';
$lang['post_reply_error'] = '回复失败了！～';
$lang['post_reply_success'] = '回复已发布成功';

$lang['faild_request'] = '非法请求';

$lang['taboo_import_success'] = "监测到敏感词：@{total_num}个；成功录入：@{suc_num}个；录入失败：@{fail_num}个。";

//Add by Liuw:马甲相关
$lang['vest_user_has_not_here'] = '指定的用户不存在';
$lang['vest_user_password_error'] = '密码不正确';
$lang['vest_user_has_added'] = '该用户已经是马甲了';
$lang['vest_add_error'] = '添加马甲失败了！请联系管理员检查系统';
$lang['vest_add_success'] = '马甲添加成功';
$lang['vest_delete_success'] = '马甲删除成功';
$lang['vest_dis_success'] = '马甲派发成功';

//Banner相关
$lang['banner_has_on_five'] = '最多只能启用5个banner！';
$lang['banner_on_success'] = '指定的Banner已成功启用';
$lang['banner_off_success'] = '指定的Banner已成功禁用';
$lang['banner_edit_success'] = 'Banner属性修改成功';
$lang['banner_set_top_success'] = '新的Banner排序策略已保存';
$lang['banner_delete_success'] = 'Banner已删除';
$lang['banner_add_error'] = '添加Banner失败了！';
$lang['banner_add_success'] = 'Banner已成功添加';
$lang['banner_image_empty'] = 'Banner图片没有传够，@{size}张图片都必须上传';
$lang['banner_order_error'] = '排序值必须是10的整数倍数';
$lang['banner_order_has_used'] = '排序值不能重复使用';

//Background相关
$lang['background_image_empty'] = '背景图片规格不足，@{size}张图片都必须上传';

//Reply相关
$lang['reply_receiver_not_here'] = '接收者帐号不存在';
$lang['reply_item_not_empty'] = '没有指定回复主体';
$lang['reply_vest_has_empty'] = '你还没有设置马甲，请先设置再回复';

//敏感词相关
$lang['taboo_check_faild'] = '请至少选择一个敏感词';

// 商品来源
$lang['goods_source_name'] = 'IN沈阳';

//Add by Liuw：CMS相关
$lang['cms_cat_name_used'] = '这个分类已经建立了，请更换分类名称';
$lang['cms_cat_add_success'] = '分类创建成功';
$lang['cms_cat_add_fail'] = '分类创建失败';
$lang['cms_cat_edit_success'] = '分类已成功修改';
$lang['cms_cat_edit_fail'] = '分类修改失败';
$lang['cms_cat_delete_success'] = '所选分类已删除';
$lang['cms_cat_delete_fail'] = '分类删除失败';
$lang['cms_content_empty_sub'] = '标题不能为空';
$lang['cms_content_empty_cont'] = '内容不能为空';
$lang['cms_content_empty_source'] = '来源媒体不能为空';
$lang['cms_content_empty_jump'] = '来源链接不能为空';
$lang['cms_content_empty_inc'] = '来源时间不能为空';
$lang['cms_content_empty_cid'] = '文章分类不能为空';
$lang['cms_content_add_success'] = '文章已保存';
$lang['cms_content_add_fail'] = '文章保存失败了';
$lang['cms_content_edit_success'] = '文章已编辑';
$lang['cms_content_del_success'] = '文章已删除';
$lang['cms_content_exa_success'] = '文章已发布';

//Add by Liuw:模拟客户端取地点列表相关
$lang['poi_category_id_null'] = '请选择一个地点';

//Add by Liuw:推荐相关
$lang['rec_frag_no_fid'] = '请选择一个碎片查看';
$lang['rec_frag_is_null'] = '选择的碎片不存在或已删除';
$lang['rec_frag_link_success'] = '碎片已成功地关联到指定频道了';
$lang['rec_frag_unlink_success'] = '已成功取消了碎片与频道的关联';
$lang['rec_frag_del_success'] = '碎片已删除';
$lang['rec_frag_make_success'] = '碎片已保存';
$lang['rec_frag_name_used'] = '碎片名称重复了，请修改后重新提交';

