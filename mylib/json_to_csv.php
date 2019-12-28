<?php
/**
 * Convert json to csv
 * author : Brokenpen(me)
 * date : 2015/11/26/ 21:08
 * status : success , tested
 * // testing part at the end..
 * 
 */

//echo "say first line\n";
	
require_once("./do_overwrite.php");
//require_once("./print_r_to_html.php");
	
	function json_to_csv($json_filename, $csv_filename, $csv_delimiter = ",", $overwrite = false)
	{
		//echo "Hi I'm json_to_csv function\n";
			if( ($array = json_decode(file_get_contents($json_filename),false)) == !false)
			{
				//print_r($array);
				//print_r_to_html("../test/test_json_array.html",$array);
				
				if(do_overwrite($csv_filename,$overwrite) == false)
				{
					//echo "Hi I in do_overwrite == false\n";
					return false;
				}
				
				//echo "Hi fucker, bye fucker\n";
				if( ($fp = fopen($csv_filename, 'w')) == !false)
				{
					
					//echo "Hi I in fopen == !false\n";

					$line_index = 0;
					$line_length = count($array);	## count how  many line...
					$content = "";
					var_dump($line_length);
					
					
					//echo "Last key : ". $last_key ."\n";
					foreach ($array as $line) 
					{
						end($line);         ## move the internal pointer to the end of the array
						$last_key = key($line);	
	
						## this code fail, <int>1 <---
						//$key_length = count($line);	## count how  many element ...
						//var_dump($key_length); ## <int>1
						
						++$line_index;	## add line number..
						
							$key_index = 0;

							foreach ($line as $key => $value) 	## in line
							{
								echo $key;
								//break;
								if($line_index === 1)	## first line.. store as header
								{
									++$key_index;
									$content .= $key;
									if($key !== $last_key)
										$content .= $csv_delimiter;
									else if($key == $last_key)
									{	echo "new line\n";
										$content .= "\n";
									}
								}
								else 
								{
									++$key_index;
									$content .= $value;
									//if($key_index !== $key_length)
									if($key !== $last_key)
										$content .= $csv_delimiter;
									else if($key == $last_key)
										$content .= "\n";
								}
								
							}	## end foreach in a line
						
						
						//fwrite($csv_filename, $content);
					}	## end foreach line
					fwrite($fp, $content);
					fclose($fp);
					return true;	## json_to_csv success
				}
				else 
					return false;
			}	## end json
			else
				return false;
	}	## function end
	
	
	## output all errors messenage 
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	##var_dump(json_to_csv());
	
	##testing
	//json_to_csv();
	//json_to_csv("G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/optab.json","G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/test/result.csv");
	//var_dump(json_to_csv("G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/optab.json","G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/test/result.csv",",",true));
	
	
	
?>


	