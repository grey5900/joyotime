<div class="page-wrapper">
    <ul class="breadcrumb">
        <li>
            <i class="i-left"></i>
            <a href="/partners/index"  class="active">
                <?php echo __('内容合作商列表'); ?>
            </a>
        </li>
        <li>
            <span>
                <strong>“<?php echo $partner['name'];?>” 鱼说统计</strong>
            </span>
        </li>
    </ul>
<?php
    $this->Voice->rows = $items;
?>
    <div class="tab-content clearfix active">
        <div class="box-navtool">
         <form class="navbar-form" name="search" action="/partners/statistics/<?php echo $id;?>" method="get">
           <table>
           	<tr>
           		<td>首次上架时间&nbsp;</td>
           		<td> <input type="text" style="width:80%" value="<?php echo $startTime;?>" id="startTime" name="startTime" onClick="WdatePicker({readOnly:true})"/>
           		<img src="/js/plug-in/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle" style="cursor:pointer" onclick="WdatePicker({el:'startTime',readOnly:true})">
           		</td>
           		<td>-</td>
           		<td>&nbsp;&nbsp;<input type="text" value="<?php echo $endTime;?>" style="width:80%" name="endTime" id="endTime" onClick="WdatePicker({readOnly:true})"/> 
           		<img src="/js/plug-in/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle" style="cursor:pointer" onclick="WdatePicker({el:'endTime',readOnly:true})">
           		</td>
           		<td><input type="button" id="search_btn" value="查询"  class="btn btn-primary"/></td>
           		<td><input type="button" value="导出" id="export_btn" class="btn btn-primary" /></td>
           	</tr>
           </table>
           </form>
        </div>
        <div><?php if ($startTime || $endTime) { ?><span>指定时段内：</span><?php } ?>共上架<?php echo $total;?>条鱼说，总时长<?php echo $this->Partner->formatTime($length);?>，总收入<?php echo $this->Partner->formatTime($earn_total);?></div>
        <table class="table table-condensed list-table">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '25%')),
                    array(__('Language') => array()),
                    array(__('Length')=> array()),
                    array(__('Score')=> array()),
					array(__('Comments')=> array()),
                    array(__('Author') => array('width' => '10%')),
                    array(__('销量')=> array()),
                    array(__('总收入')=> array()),
                    array(__('首次上架时间') => array('width' => '10%')),
                    array(__('审核者') => array('width' => '8%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php   foreach($items as $idx => $row): ?>
                <?php 
               
                $this->Voice->init($row); ?>
                <tr class="">
                   <td>
                        <div class="intro">
                            <div class="pull-left">

                                <a data-href="<?php echo $this->Voice->cover('source'); ?>" href="#modalLightbox" class="thumbnail" data-toggle="modal" data-lightbox>
                                    <img src="<?php echo $this->Voice->cover(); ?>" alt="" />
                                </a>
                            
                            </div>
                            <div class="details">
                                <p><?php echo $this->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address() ?>" href="#modalPlay"></a>
                                <?php $point = $this->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-map-id="map-<?php echo $idx; ?>" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><a class="icon-qrcode" data-qrcode="modal" data-qrid="<?php echo $idx; ?>" data-qrurl="<?php echo $this->Voice->orurl($this->Session->read('Api.Token.web_host')) ?>" data-qrtitle="<?php echo $this->Voice->title() ?>" href="#modalQrcode"></a>
                                </div>
                                <?php echo $this->Voice->isfree(); ?>
                                <div class="qrimage-raw" id="qrimage-<?php echo $idx; ?>" style="display: none">
                                	<?php echo $this->Voice->orimage($this->Voice->orurl($this->Session->read('Api.Token.web_host'))); ?>
                                </div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length();?></td>
                    <td><?php echo $this->Voice->score() ?></td>
                    <td><a href="/comments/index?voice=<?php echo $this->Voice->id(); ?>"><?php echo $this->Voice->commentTotal() ?></a></td>
                    <td><?php echo $this->Voice->author('',1) ?> <Br /><?php echo $this->Voice->rolename() ?></td>
                    <td><?php echo $this->Voice->checkoutTotal() ?></td>
                    <td><?php echo $this->Voice->earnTotal() ?></td>
                    <td><?php echo $this->Voice->approvedTime() ?></td>
                    <td>管理员<?php // echo $this->Voice->getApproveType() ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/qrcode'); ?>
<?php echo $this->element('/modals/unavailable'); ?>
<?php echo $this->element('/modals/invalid'); ?>
<?php echo $this->element('/modals/lightbox'); ?>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    echo $this->Html->script('/js/plug-in/My97DatePicker/WdatePicker.js');
    $this->end();
?>

<?php 
$this->start('script'); ?>
<script type="text/javascript">

$(function() {
    $("#search_btn").on("click", function(){
        submit_search('list');
        return false;
    });
    
    $("#export_btn").on("click", function(){
        submit_search('export');
        return false;
    });
    function submit_search(type){
       
        if(type=='export' && $('.list-table tr').length==1){
       	 	$.messager('没有数据可以导出');
			return false;
        }
        var input = $("input[name=startTime]");
        var endTime = $("input[name=endTime]").val();
        var form = input.parents('form');
        var startTime = input.val();
        var url = form.attr('action');
        window.location.href = url + "/?startTime=" + startTime+'&endTime='+endTime+'&type='+type;
    }
   
})
</script>
<?php
$this->end();
?>