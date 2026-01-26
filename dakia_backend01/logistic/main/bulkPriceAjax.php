<?php
if(isset($_POST['request']) && trim($_POST['request'] =='ajax')){
   sleep(10);
  echo "<pre>";print_r($_POST);echo "</pre>"; die;
}
?>