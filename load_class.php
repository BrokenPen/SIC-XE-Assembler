<?php

$excludeFile = array("test.php", "crash.php",
					"GenerateObjectCode_old_vwork.php",
					"GenerateObjectCode_wrong - Copy.php"
					);


// $path = "./class/";
$path = CLASS_PATH;

foreach (glob($path."*.php") as $filename)
{
	if(!in_array(basename($filename),$excludeFile))
		require_once ($filename);
}
?>