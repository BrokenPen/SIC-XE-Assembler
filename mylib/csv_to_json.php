<?php

/* function : csv_to_json
 * Author = Brokenpen (me)
 * Date = 2015/11/26 21-23
 * status = sccess , tested
 */

require_once(dirname(__FILE__)."/csv_to_array.php");
require_once(dirname(__FILE__)."/do_overwrite.php");
	
	function csv_to_json($csv_filename,$json_filename,$csv_delimiter = ",",$overwrite = false)
	{

		if( ($array = csv_to_array($csv_filename,$csv_delimiter)) == !false )
		{
			//file_put_contents($json_filename,json_encode($array));	## file_put_contents do oewrwrite
			if ( do_overwrite($json_filename,$overwrite) == false )
				return false;
			
			if( ($fp = fopen($json_filename, 'w')) == !false)
			{
				fwrite($fp, json_encode($array));
				fclose($fp);
				return true;
			}
			else
			{
				fclose($fp);
				return false;
			}
			
		}
		else
			return false;
	}

/* testing */
#	ini_set('display_errors', 1);
#	ini_set('display_startup_errors', 1);
#	error_reporting(E_ALL);
#	csv_to_json("G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/optab-tab.csv","../test/test_csv_to_json.json");
?>