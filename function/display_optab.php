<?php


	function display_optab()
	{
		global $OPTAB;

		## display operand code	## the column name
		echo str_pad("Mnemonic",10).str_pad("Format",10).str_pad("Opcode",10);
		echo "\n";

		## real column name	OPCODE	FORMAT	OPCODEVAL	P	X	F	C	
		foreach ($OPTAB as $_OPTAB)
		{
			echo str_pad($_OPTAB['OPCODE'],10).str_pad($_OPTAB['FORMAT'],10).str_pad($_OPTAB['OPCODEVAL'],10);
			echo "\n";
		}
	}

	




?>