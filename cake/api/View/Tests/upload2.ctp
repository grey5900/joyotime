<?php

?>

<form method="post" action="http://up.qiniu.com/" enctype="multipart/form-data">
  <input name="token" type="hidden" value="<?php echo $token ?>">
  <input name="file" type="file" />
  <button type="submit">Submit</button>
</form>