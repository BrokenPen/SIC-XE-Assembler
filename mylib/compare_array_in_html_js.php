<?php

require_once(dirname(__FILE__)."/create_file.php");

function compare_array_in_html_js($fileName, $arr1, $arr2, $title = 'compare_array', $overWrite = false) {
	$content = "";
	
	$jsArr1 = json_encode($arr1);
	$jsArr2 = json_encode($arr2);
	
	$content .= "
		<html>
		<head><title>". $title . "</title></head>
		<body>
			<script type='text/javascript'>
			var jsArr1 = ".$jsArr1. ";
			
			var jsArr2 = " .$jsArr2."
			document.write(jsArr1);
			document.write(jsArr2);
			</script>
		</body>
		</html>
	";
	
	create_file($fileName, $content, $overWrite);
}



?>