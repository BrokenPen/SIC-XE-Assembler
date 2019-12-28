<?php

## author = Brokenpen ( me )
## modify date = 2015/11/26 17:43pm
## status = success...

	function do_overwrite($filename="",$overwrite = false)
	{
		//echo "Hi I'm do_overwrite\n";
		if(empty($filename) == !false)
		{
			//echo "Hi I'm in empty\n";
			return false;
		}
		else
		{
			//echo "Hi I'm in else\n";
			if($overwrite == false)
			{
				//echo "Hi I'm in false\n";
				if(file_exists($filename) == false)				## not exist
				{
					//echo "Hi I'm in file_exist false\n";
					##touch($filename);	## create error.log if not exist...
					if( fopen($filename, "w") == !false)
					{
						return true;
					}
					else
					{
						fclose($filename);
						var_dump(error_get_last());
						return false;
					}	## end else
					
				}
			}
			else ## if($overwrite == true)
			if(file_exists($filename) == true)
			{
				//echo "Hi I'm in true\n";
				unlink($filename);	## delete if  exist...
				##touch($filename);  ## create a empty file now
				if( fopen($filename, "w") == !false)
				{
					return true;
				}
				else
				{
					fclose($filename);
					var_dump(error_get_last());
					return false;
				} ## end else
				
			}
			
			return file_exists($filename);	
		}
	}
	
	## testing code ##
	/*
	var_dump(do_overwrite());
	do_overwrite("G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/test/result_tab.csv");
	sleep(60);
	do_overwrite("G:/inetpub/wwwroot/d/ntcu/104/System_Software/project/myAssembler/test/result_tab.csv",true);
	*/
	
?>