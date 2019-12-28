<?php

require_once(dirname(__FILE__).'/do_overwrite.php');

	function print_r_to_html($fileName, $value,$title="")
	{
				if(file_exists($fileName) == !false)
				{
					//unlink($fileName);	## deleter the last time running result file..
				}
				else
					do_overwrite($fileName, false);	## create error.log if not exist...

				## Operand table(with object code) result store in html to present...
				$optb_woc_File = fopen($fileName, "w") or die("Unable to open file!");
				$log = "
						<html>
						<head>
						<title>".$title."</title>
						</head>
						<body>
						";
				$log .= "<pre>". print_r($value,true) ."</pre>";
				$log .= "
						</body>	
						</html>
						";
						
				fwrite($optb_woc_File, $log);
				fclose($optb_woc_File);
	}
	
?>