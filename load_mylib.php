<?php

$excludeFile = array("test.php", "crash.php");

// $path = "./mylib/";
$path = MYLIB_PATH;

foreach (glob($path."*.php") as $filename)
{
	if(!in_array(basename($filename),$excludeFile))
		require_once ($filename);
}
?>