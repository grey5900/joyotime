<div class="alert <?php echo isset($class) ? $class : 'alert-info';?>">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <?php echo isset($message) ? $message : ''; ?>
</div>
