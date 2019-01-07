<div class="navbar navbar-inverse <?php echo $class ?>">
	<div class="container">
		<div class="fs-nav-logo clearfix">
			<a href="/" class="fs-logo"><img src="/img/<?php echo __('logo_cn') ?>.png" alt=""/> </a>
		</div>
		<nav class="collapse navbar-collapse fs-navbar-collapse">
			<ul id="chang-lang" class="nav navbar-nav">
				<li>
				    <?php 
                        if(Configure::read('Config.language') == 'chn') {
                            echo $this->Html->link('English', '/langs/eng');
                        } else {
                            echo $this->Html->link('中文', '/langs/chn');
                        }
                    ?>
			    </li>
			</ul>
		</nav>
		<?php //if($class == 'fs-home-header'): ?>
		<?php //echo $this->element('address'); ?>
		<?php //endif; ?>
	</div>
</div>