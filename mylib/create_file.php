<?php

require_once( dirname(__FILE__)."/do_overwrite.php");

function create_file($filename, $content, $overwrite = false){
	if( do_overwrite($filename,$overwrite) == !false)
		file_put_contents($filename, $content);
}
	
?>