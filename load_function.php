<?php

$excludeFile = array("test.php", "crash.php");

// $path = "./function/";
$path = FUNCTION_PATH;

foreach (glob($path."*.php") as $filename)
{
	if(!in_array(basename($filename),$excludeFile))
		require_once ($filename);
}
?>