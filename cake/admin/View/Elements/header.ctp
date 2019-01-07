<div class="navbar">
    <div class="navbar-nav-logo">
        <a href="/voices" class="logo"  title="FishSaying-鱼说">
            <cite><?php echo __('Welcome to contributor platform of fish saying'); ?></cite>    
        </a>
    </div>
    <div class="navbar-nav-menu">
        <span class="subtitle"><?php echo __('Hi dude'); ?>，<em><?php echo CakeSession::read('Auth.User.username') ?></em></span>
       <!--  <a href="/users/lang/zh_cn" class="lagge"><?php echo __('Chinese')?></a>
        <a href="/users/lang/en_us" class="lagge"><?php echo __('English')?></a>--> 
        <a href="/users/logout" class="logout-close"><i class="icon-close"></i><?php //echo __('Logout')?></a>
    </div>
</div>