<?php
# how to explain the code?
/********************************************************************
 *You probably can skip some dull function,						    *
 * direct to the important function									*
 *		There are:													*
 *				1. readTxt											*
 *				2. readCsv											*
 ********************************************************************/
 
# The coding style
/********************************************************************
 *Make code with hight readability									*
 *keep it clarity and short as possible								*
 *Try to make comment to all code									*
 ********************************************************************/

# Import note : the progress of this file
# Import note : the progress of this file
# Import note : the progress of this file
/********************************************************************
 *The function below are not finish yet			not finish yet      *
 * 				setDefaultDirectory									*
 ********************************************************************/
 
# A class for reading file(symbol table) return in array
class ReadFile
{
	
	private $OPTB = array();		## initial a symbol table array
	private $defaultDirectory = "";	## initial the defaultDirecotry path ## no finish yet

	## getoff stop coding no use function/feature
	//private $settingsFile = "";		## initial the settingsFile	#default by function is sic.ini
	
	## constructor .... import... pre initialization
	function __construct() 
	{
		## loadDefaultDirecotry not finish yet
		//$this->loadDefaultDirecotry();
		print "In constructor\n";
		$this->name = "ReadFile";
	}

	/* ## finish it when you are bored to hell 
	## load the default directory setting from a sic.in 
	public function loadDefaultDirecotry()
	{
		if($handle = fopen("./sic.ini","r")  == !false)
		{
			$defaultDirectory = fread($handle,filesize("sic.ini"))
		}
		else if(file_exists(" == false)
		{
		
		}
		else
		{
		
		}
		
	}
	*/
	
	# not finish yet
	# setDefaultDirectory use to set default path where the input exist
	public function setDefaultDirectory($directory = "./Figure", $pathType ="relative")
	{
		return true;	## the path or directory are valid
	}
	
	# group with setDefaultDirectory so # not finish yet
	# return the defaultDirecotory
	public function getDefaultDirectory()
	{
		return $this->defaultDirectory;
	}
	
	# readFile determine file name validation
	public function readFile($fileName = '')
	{
		if(empty($fileName) == !false)
		{
			echo "Error: empty faileName input, program exit...\n"; 
			return false;
		}
		else if(file_exists($fileName) == false)	## determine it is a exist file
		{
			echo "The ".$fileName." file do not exist.\n";
			echo "\n";
			return false;
		}
		else	## file_exists == true
		{
			$path_parts = pathinfo($fileName);
			
			if($path_parts['extension'] == 'txt')
			{
				return $this->readTxt($fileName);
				// return true;
			}
			else if($path_parts['extension'] == 'csv')
			{
				return $this->readCsv($fileName);
				// return true;
			}
			else	## exclude file extension
			{
				echo "File extension do not support, please enter txt or csv file name";
				return false;
			}
				
		}
		
	}
	
	# read txt file and return a array	## only fix to well formatted txt file, otherwise will generate execute error 
	private function readTxt($fileName = '')
	{
		echo "readTxt Ordered.\n";
		
		## initialization
		$_OPTB['LINE'] 		= 0;	//	<- Line
		$_OPTB['OPCODE'] 	= NULL;
		$_OPTB['OPERAND']	= NULL;
		$_OPTB['COMMENT']	= NULL;
	
		$file = fopen ($fileName,"r");	## open file as read 
		$lnVar = 0;						## initial the line of reading in the txt
		while(feof($file) == false)				## not the end of file
		{
			$line = fgets($file);	## getting the current line content
			$lnVar +=1;				## reading a line then add 1 to lnVar
			
			##	reg_split the content and store in to array[$column]
			//$column = preg_split('/\s+/', $line);	# To separate by spaces/tabs/newlines:
			$column = preg_split("/[\t]/", $line);	# To separate by tabs
			
			## to settle the empty field 
			for($index = 0; $index <= 3; $index++)
			{
				if(isset($column[$index]) || !empty($column[$index]))
				{
				}
				else
				{
					$column[$index] = "";	## only always empty in array, if use NULL will don't have the respond index... 
				}
				
			}
			
			$_OPTB['LABEL'] 		= $column[0];
			$_OPTB['OPCODE'] 		= $column[1];
			$_OPTB['OPERAND']		= $column[2];
			$_OPTB['COMMENT']		= $column[3];
		
			## this feature no use now, no need to worry wrong formatted file now..
			/*
			if(strpos($_OPTB['VALUE'],'SUBROUTINE') !== false)
			{
				$_OPTB['VALUE'] == "WTF";
				$_OPTB['COMMENT'] == "";
			}
			*/
			
			## ignore this scope comment totally bullshit
			## $lineVar value increase/decrease for different condition
			## need to complete this
			/*
			if(some_confition equal true)
			{
				$lineVar += value; //
			}
			*/
			## In Developing state: just set for +=5
			$_OPTB['LINE']+=5;
		
			$lineTmpArr = array(
								"LINE" 		=> 	trim($_OPTB['LINE']), 
								"LABEL" 	=>	trim($_OPTB['LABEL']), 
								"OPCODE" 	=>	trim($_OPTB['OPCODE']), 
								"OPERAND" 	=>	trim($_OPTB['OPERAND']),	
								"COMMENT" 	=>	trim($_OPTB['COMMENT'])
								);
			
			## Add each line in objectCodeArr;
			array_push($this->OPTB, $lineTmpArr);		
		}
		fclose($file);

		## return the operand table
		## debugging
			##print_r($this->OPTB);
			##break;
		return $this->OPTB;
	}
	
	# read csv file and return a array
	private function readCsv($fileName = '')
	{
		## use csv_to_array.php ... csv_to_array function
		echo "readCsv Ordered.\n";
		## debugging
			##print_r(csv_to_array($fileName,"\t"));
			##break;
		return $this->OPTB = csv_to_array($fileName,",");
	}
	
	function __destruct() 
	{
		print "Destroying " . $this->name . "\n";
	}
	
}

?>