<?php

	## output all errors messenage 
	ini_set('display_errors', 1);	// undefine index label.. need to fix... but let me finish the Figure2.16 first
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


		/****************************************************************
		 * @ You should learn the magic constant in php					*
		 * http://php.net/manual/en/language.constants.predefined.php 	*
		 ****************************************************************/
		define("RESULT_PATH", dirname(__FILE__)."/result/");
		echo "REUSLT_PATH . \n";
		echo RESULT_PATH . "\n";


		define("FIGURE_PATH",dirname(__FILE__)."/figure/");
		define("ORIGIN_FILE_PATH",dirname(__FILE__)."/origin_file/");
		define("FUNCTION_PATH", dirname(__FILE__)."/function/");
		define("MYLIB_PATH", dirname(__FILE__)."/mylib/");
		define("CLASS_PATH", dirname(__FILE__)."/class/");

		function createFolder() {
			$folderArr = array('result', 'figure', 'origin_file', 'function', 'mylib', 'class');
			foreach($folderArr as $folderName) {
				if (!file_exists(dirname(__FILE__).'/'.$folderName)) {
					mkdir(dirname(__FILE__).'/'.$folderName, 0777, true);
				}
			}
		}
		createFolder();




		## require some data from a external souces.
		//include_once(dirname(__FILE__)."/optab.php");			## haha I use $SYMTB = csv_to_array('symtb.csv',"\t"); for instead now

		## require some function
		//require_once("./load_mylib.php"); // error - -
		//require_once("./load_function.php"); // error - -


		require_once(MYLIB_PATH."compare_array_in_html_js.php");	## compare array in js
		require_once(MYLIB_PATH."array_array_key_filter.php");	## array array filter
		require_once(MYLIB_PATH."array_search_value.php");	## search array
		require_once(MYLIB_PATH."do_overwrite.php");	## search array
		require_once(MYLIB_PATH."strposa.php");	## strpos with array , also with my advance stupid edit
		require_once(MYLIB_PATH."delTree.php");	## delTree
		require_once(MYLIB_PATH."print_r_to_html.php");	## store print_r($value) into html

		require_once(MYLIB_PATH."get_string_between.php");		## csv to array..
		require_once(MYLIB_PATH."csv_to_array.php");		## csv to array..
		require_once(MYLIB_PATH."is_in_array.php");		## is_in_array..

		require_once(FUNCTION_PATH."display_optab.php");	## csv to array..
		require_once(FUNCTION_PATH."optb_to_optb_woc.php");	## operand table to operand table(with object code)
		require_once(FUNCTION_PATH."display_optb_woc.php"); ## display operand table(with object code)


		## require some class
		//require_once("./load_class.php"); // error - -
		require_once(CLASS_PATH."ReadFile.php");			## read operand table , txt or csv
		## Deloveping now...
		require_once(CLASS_PATH."GenerateObjectCode.php");
		require_once(CLASS_PATH."GenerateObjectProgram.php");
		## downloaded class..
		require_once(CLASS_PATH."class.Diff.php");



	## template for output log
	define("OUTPUT_LOG_PREFIX",  date("Y-m-d-His")."_");
	echo "Now : " . OUTPUT_LOG_PREFIX . "\n";
	//echo "Now : ". OUTPUT_LOG_PREFIX ."\n";
	
	
	define("EORROR_LOG_FILE",dirname(__FILE__)."/error.log");
	if(file_exists(EORROR_LOG_FILE) == false)
		do_overwrite(EORROR_LOG_FILE);	## create error.log if not exist...
	## make sure error.lg are empty 
	$logFile = fopen(EORROR_LOG_FILE, "w") or die("Unable to open file!");
	$log = "Error " . OUTPUT_LOG_PREFIX. " : ";
	fwrite($logFile, $log);
	fclose($logFile);
	
	ini_set("log_errors", 1);
	ini_set("error_log", "error.log");
	
	
	

	
	# generate the sic symbol table[array] from a symtb.csv
	# global $SYMTB use in class...
	/****************************************************************
	 *	Note : I also add WD TD in the optab csv... too				*
	 ***************************************************************/
	/****************************************************************
	* @ 															*
	* stny said read csv is slow, use json faster read. so I use - *
	****************************************************************/
	
	#*********** Generate json file ********************************
	//$OPTAB = csv_to_array('./optab-tab.csv',"\t");	## can't fetch line 60 TD....
	//$OPTAB = csv_to_array('./optab-comma.csv',",");	
	//file_put_contents("optab.json",json_encode($OPTAB));
	#*****************************************************************/
	#************Get operand table form optab.json*******************#
	$OPTAB = json_decode(file_get_contents("optab.json"),true);
	#************ Fixing my optb json file ***************************#
	/*
	$OPTB_FIX = array();
	foreach($OPTAB as $value) {
		if(strlen($value['OPCODEVAL']) == 1)
			$value['OPCODEVAL'] = "0" . $value['OPCODEVAL'];
		array_push($OPTB_FIX, $value);
	}
	$OPTAB = $OPTB_FIX;
	file_put_contents("optab_fix.json",json_encode($OPTB_FIX));
	*/

	

?>