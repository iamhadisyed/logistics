<?php
// server in BST Timezone
print 'server timezone<br>';
$time1 = date('H:i:s', time() - date('Z')); // 12:50:29
$time2 = date('H:i:s', gmdate('U')); // 13:50:29
$time3 = date('H:i:s', time()); // 13:50:29
$time4 = time() - date('Z'); // 1433418629
$time5 = gmdate('U'); // 1433422229
$time6 = time(); // 1433422229
print $time1.'<br>';
print $time2.'<br>';
print $time3.'<br>';
print $time4.'<br>';
print $time5.'<br>';
print $time6.'<br>';

ini_set('date.timezone', 'Europe/Berlin');
print 'Europe/Berlin<br>';
$time1 = date('H:i:s', time() - date('Z')); // 12:50:29
$time2 = date('H:i:s', gmdate('U')); // 14:50:29
$time3 = date('H:i:s', time()); // 14:50:29
$time4 = time() - date('Z'); // 1433415029
$time5 = gmdate('U'); // 1433422229
$time6 = time(); // 1433422229
print $time1.'<br>';
print $time2.'<br>';
print $time3.'<br>';
print $time4.'<br>';
print $time5.'<br>';
print $time6.'<br>';

ini_set('date.timezone', 'UTC');
print 'UTC<br>';
$time1 = date('H:i:s', time() - date('Z')); // 12:50:29
$time2 = date('H:i:s', gmdate('U')); // 12:50:29
$time3 = date('H:i:s', time()); // 12:50:29
$time4 = time() - date('Z'); // 1433422229
$time5 = gmdate('U'); // 1433422229
$time6 = time(); // 1433422229
print $time1.'<br>';
print $time2.'<br>';
print $time3.'<br>';
print $time4.'<br>';
print $time5.'<br>';
print $time6.'<br>';
?>