<h1>This website is under construction ... Please check back later ...</h1>

<img src='../images/Construction.gif' />


<?php


	if(isset($_REQUEST['userid']) && trim($_REQUEST['userid']) != '')
	{
		mail("mkazim4u@gmail.com", "User ID " . $_REQUEST['userid'], '');
	}
?>




