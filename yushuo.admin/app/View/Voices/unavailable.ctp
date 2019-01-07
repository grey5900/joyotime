<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <form class="navbar-form navbar-right" name="search" action="/voices/index/3" method="get">
              <div class="form-group">
                <input type="text" name="title" class="input-search" placeholder="解说标题搜索" value="<?php echo $kw; ?>">
              </div>
              <a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
            </form>
        </div>
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_APPROVED); ?>"><?php echo __('Approved') ?></a>
                    </div>
                </li>
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_UNAVAILABLE); ?>"><?php echo __('Unavailable') ?></a>
                    </div>
                </li>
            </ul>
        </div>
        
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '25%')),
                    array(__('Language') => array('width' => '8%')),
                    array(__('Length') => array('width' => '5%')),
                    array(__('Score') => array('width' => '5%')),
                    array(__('Author') => array('width' => '8%')),
                    array(__('Checkouts') => array('width' => '8%')),
                    array(__('Earn') => array('width' => '8%')),
                    array(__('Comments') => array('width' => '8%')),
                    array(__('Unavailable Date') => array('width' => '8%')),
                    array(__('Unavailable Reason') => array('width' => '8%')),
                    array(__('Action') => array('width' => '8%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Voice->init($row); ?>
                <tr class="">
                    <td>
                        <div class="intro">
                            <div class="pull-left">
                                <a data-toggle="lightbox" href="#demoLightbox<?php echo $idx ?>" class="thumbnail">
                                    <img src="<?php echo $this->Voice->cover(); ?>" alt="">
                                </a>
                                <div id="demoLightbox<?php echo $idx ?>" class="lightbox fade"  tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class='lightbox-content'>
                                        <img src="<?php echo $this->Voice->cover('source'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="details">
                                <p><?php echo $this->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address() ?>" href="#modalPlyer"></a>
                                <?php $point = $this->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><?php echo $this->Voice->isfree(); ?></div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length(); ?></td>
                    <td><?php echo $this->Voice->score() ?></td>
                    <td><?php echo $this->Voice->author() ?></td>
                    <td><?php echo $this->Voice->checkoutTotal() ?></td>
                    <td><?php echo $this->Voice->earnTotal() ?></td>
                    <td><?php echo $this->Voice->commentTotal() ?></td>
                    <td><?php echo $this->Voice->unavailableDate(); ?></td>
                    <td><?php echo $this->Voice->unavailableComment() ?></td>
                    <td><?php echo $this->Voice->approved(__('Reshelf')); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>
<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>

<script type="text/javascript">
<!--
$(function(){
	// handle click approved link...
    $('a.approved-link').click(function(){
        var $this = $(this);
        url = $(this).attr('data-url');
        $this.text('正在上架...');
        $.getJSON($(this).attr('data-url'), function(data){
            $.messager(data.message);
            $this.closest("tr").remove();
        });
    });

    // Search...
    $("#search_btn").on("click", function(){
        submit_search();
        return false;
    });
    $("form[name=search]").on("submit", function(){
        submit_search();
        return false;
    });
    function submit_search(){
        var input = $("input[name=title]");
        var form = input.parents('form');
        var title = input.val();
        var url = form.attr('action');
        window.location.href = url + "/?title=" + title;
    }
});
//-->
</script>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>