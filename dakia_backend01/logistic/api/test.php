<?php
@ini_set('output_buffering','Off');
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
@ob_end_clean();
set_time_limit(0);
ob_start();

//echo str_repeat('        ',1024*8); //<-- For some reason it now even works without this, in Firefox at least?
?>
<!DOCTYPE html>
<html>
<head>
	<title>Flushing TEST</title>
</head>
<body>
<h1>Flushing PHP.</h1>
<?php
ob_flush();
flush();

//Note: ob_flush comes first, then you call flush. I did this wrong in one of my own scripts previously.
for($i=0; $i<5; $i++) {
	echo $i."<br> Line to show.";
	echo str_pad('',4096)."\n";
	ob_flush();
	flush();
	sleep(1);
}
?>
</body>
</html>
<?php
phpinfo();
?>