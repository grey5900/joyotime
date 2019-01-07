<?php
include '../include/common.inc.php';
include PHPCMS_ROOT.'/include/attachment.class.php';

if(!$_userid && !$_SESSION['cid']) showmessage("<script>alert('您还没有登录，不允许上传文件！');</script>");
if(!isset($_FILES['uploadfile'])) showmessage("<script>alert('请选择文件并上传！');</script>");

$att = new attachment;

$uploadfiletype = '';
if($channelid)
{
	if(!array_key_exists($channelid, $CHANNEL) || $CHANNEL[$channelid]['islink'] == 1) showmessage("<script>alert('非法参数！');</script>");
	include PHPCMS_ROOT.'/include/channel.inc.php';
	@extract($CHA);
	$savepath = $PHPCMS['uploaddir'].'/'.$channeldir.'/'.$uploaddir.'/'.date('Ym').'/';
}
else
{
	$MOD = cache_read($mod.'_setting.php');
	if(!$MOD) showmessage("<script>alert('非法参数！');</script>");
	@extract($MOD);
   $savepath = $mod == 'phpcms' ? $PHPCMS['uploaddir'].'/'.date('Ym').'/' :  $PHPCMS['uploaddir'].'/'.$moduledir.'/'.$uploaddir.'/'.date('Ym').'/';
}

if(isset($enableupload) && !$enableupload) showmessage("<script>alert('系统禁止上传文件！');</script>");

include PHPCMS_ROOT.'/include/upload.class.php';

$fileArr = array('file'=>$_FILES['uploadfile']['tmp_name'],'name'=>$_FILES['uploadfile']['name'],'size'=>$_FILES['uploadfile']['size'],'type'=>$_FILES['uploadfile']['type'],'error'=>$_FILES['uploadfile']['error']);

$uploadfiletype = $uploadfiletype ? $uploadfiletype : $PHPCMS['uploadfiletype'];
dir_create(PHPCMS_ROOT.'/'.$savepath);
$upload = new upload($fileArr,'','/uploadfile',$uploadfiletype,1,$maxfilesize);

if($upload->up())
{
	include PHPCMS_ROOT.'/include/watermark.class.php';
	include PHPCMS_ROOT.'/include/ftp.class.php';
	$imgpath = PHPCMS_ROOT.'/'.$upload->saveto;
	$water_pos = $water_pos ? $water_pos : $PHPCMS['water_pos'];
	$wm = new watermark($imgpath,50,$water_pos);
    $wm->transition = 60;
    /*
	if($PHPCMS['water_type']==1)
	{
		$water_text = $water_text ? $water_text : $PHPCMS['water_text'];
		$water_fontcolor = $water_fontcolor ? $water_fontcolor : $PHPCMS['water_fontcolor'];
		$water_fontsize = $water_fontsize ? $water_fontsize : $PHPCMS['water_fontsize'];
		$wm->text($water_text,$imgpath,$water_fontcolor,$water_fontsize,PHPCMS_ROOT.'/'.$PHPCMS['water_font']);
	}
	elseif($PHPCMS['water_type']==2)
	{
	*/
		$water_image = 'http://f.chengdu.cn/images/watermark.png';
		$wm->image($water_image,$imgpath);
	//}
    
	//$att->addfile($upload->saveto);
	$ftp = new ftp ( $CONFIG ['ftphost'], $CONFIG ['ftpuser'], $CONFIG ['ftppwd'], $CONFIG ['ftpport'], $CONFIG ['ftpdir'] );
				if (file_exists ( $imgpath ))
					$saveto = $ftp->put ( $imgpath ); 
				if (file_exists ( $filepath . $thumbname ))
					$thumb_file = $ftp->put ( $filepath . $thumbname ); 
	$ftp->ftpClose ();
				@unlink ( $imgpath );
				@unlink ( $filepath . $thumbname );

	if(!$needjump)
	{
		$message = "<script language='javascript'>";
		$message .= "alert('上传成功！');";
		$message .= "window.parent.ext_gif = '".substr($upload->saveto,-3).".gif';";
		$message .= "window.parent.phpcms_path = '".PHPCMS_PATH."';";
		$message .= "window.parent.".$uploadtext.".value='".PHPCMS_PATH.$upload->saveto."';";
		$message .= "</script>";
	}
	else
	{
		$message = "<script language='javascript'>";
		$message .= "alert('上传成功！');";
		//$message .= "window.parent.SetUrl('".PHPCMS_PATH.$upload->saveto."');";
		$message .= "window.parent.SetUrl('".$saveto."');";
		$message .= "window.parent.GetE('frmUpload').reset() ;";
		$message .= "</script>";
	}
	showmessage($message);
}
else
{
	showmessage("<script language='javascript'>alert('".$upload->errmsg()."');</script>");
}
?>