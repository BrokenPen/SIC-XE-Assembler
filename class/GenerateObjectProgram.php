<?php

/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 12/11/2015
 * Time: 2:51 PM
 */


class GenerateObjectProgram {
    private $OPTB_WOC;
    private $objectProgramContent = '';

    function __construct($OPTB_WOC) {
        $this->OPTB_WOC = $OPTB_WOC;
        $this->name = "GenerateObjectProgram";
        echo "In " . $this->name . " constructor\n";
        $this->generateObjectProgram();
    }

    private function formatTstartinggAdd($value = 0) {
        return sprintf('%06x', $value);
    }

    private function generateObjectProgram() {
        global $OUTPUT_LOG_PREFIX;

        $OPTB_WOC_OP = array();

        $headerContent = '';
        $defineContent = '';
        $referContent = '';
        $textContent = '';
        $modificationContent = '';
        $endContent = '';

        $textRecordMaxLen = hexdec('1E');
        $textRecord = '';
        $TstartinggAdd = '';
        $Tlength = 0;     // max = 1E
        $Ttemp = '';
        $perviouBlock = 0;

        if( max(array_column($this->OPTB_WOC, 'SECTION')) == 0 ) {
            if( max(array_column($this->OPTB_WOC, 'BLOCK')) > 0 ) {
                $moreThanOneBlock = true;

                $loc = array();
                $block = array();
                foreach ($this->OPTB_WOC as $key => $row) {
                    $loc[$key] = $row['LOC'];
                    $block[$key] = $row['BLOCK'];
                }
                array_multisort(array_keys($this->OPTB_WOC), SORT_ASC, $this->OPTB_WOC);

            }


            foreach ($this->OPTB_WOC as $_OPTB_WOC) {
                // testing...
                // if(!isset($_OPTB_WOC['ELOC']) || empty($_OPTB_WOC['ELOC']))  continue;

                // testing state not sure..
                // if(!isset($_OPTB_WOC['SIZE']) || empty($_OPTB_WOC['SIZE']))  continue;

                if(isset($_OPTB_WOC['realLOC']) || !empty($_OPTB_WOC['realLOC'])) {
                    $_OPTB_WOC['LOC'] = $_OPTB_WOC['realLOC'];
                }

                if(!isset($_OPTB_WOC['BLOCK']) || empty($_OPTB_WOC['BLOCK'])) {
                    // no change
                }
                else {
                    $perviouBlock = $_OPTB_WOC['BLOCK'];
                }


                if (!isset($_OPTB_WOC['LOC']) || empty($_OPTB_WOC['LOC']))
                    continue;
                else {
                    $perviouBlock = $_OPTB_WOC['BLOCK'];
                }


                if ($_OPTB_WOC['LINE'] == 5) {
                    $programName = $_OPTB_WOC['LABEL'];
                    $_OPTB_WOC['recordType'] = 'E';
                    $startingAddress = sprintf("%06X", hexdec($_OPTB_WOC['OPERAND']));
                } else if (!empty($_OPTB_WOC['OBJECTCODE'])) {
                    $_OPTB_WOC['recordType'] = 'T';
                    if ($Tlength == 0) {
                        $TstartinggAdd = '00' . $_OPTB_WOC['LOC'];
                        $Tlength = $_OPTB_WOC['SIZE'];
                        $Ttemp = $_OPTB_WOC['OBJECTCODE'] . '^';
                        // $Ttemp =  sprintf("%06X", hexdec($_OPTB_WOC['OBJECTCODE'])) . '^' ;
                        $writed = false;
                        //echo $Ttemp . "\n";
                    } else if (isset($_OPTB_WOC['ELOC']) && isset($_OPTB_WOC['LOC'])) {
                        if (($Tlength + hexdec($_OPTB_WOC['ELOC']) - hexdec($_OPTB_WOC['LOC'])) <= $textRecordMaxLen && $_OPTB_WOC['BLOCK'] == $perviouBlock ) {
                            $Tlength += $_OPTB_WOC['SIZE'];
                            $Ttemp .= $_OPTB_WOC['OBJECTCODE'] . '^';
                            // $Ttemp .=  sprintf("%06X", hexdec($_OPTB_WOC['OBJECTCODE'])) . '^' ;
                            $writed = false;
                            //echo $Ttemp . "\n";

                        } else {
                            $textContent .= 'T' . '^' . $TstartinggAdd . '^' . sprintf("%02X", $Tlength) . '^' . substr($Ttemp, 0, -1) . "\r\n";
                            $Tlength = 0;

                            $TstartinggAdd = '00' . $_OPTB_WOC['LOC'];
                            $Tlength = $_OPTB_WOC['SIZE'];
                            $Ttemp = $_OPTB_WOC['OBJECTCODE'] . '^';
                            // $Ttemp =  sprintf("%06X", hexdec($_OPTB_WOC['OBJECTCODE'])) . '^' ;
                            $writed = true;
                        }
                    }
                } else {
                    if (!$writed) {
                        $textContent .= 'T' . '^' . $TstartinggAdd . '^' . sprintf("%02X", $Tlength) . '^' . substr($Ttemp, 0, -1) . "\r\n";
                        $Tlength = 0;

                        $TstartinggAdd = '00' . $_OPTB_WOC['LOC'];

                        $Ttemp = $_OPTB_WOC['OBJECTCODE'];
                        // $Ttemp =  sprintf("%06X", hexdec($_OPTB_WOC['OBJECTCODE'])) . '^' ;
                        $writed = true;
                    }
                    $_OPTB_WOC['recordType'] = 'SKIP';
                }

                if ($_OPTB_WOC['OPCODE']{0} == '+' && in_array($_OPTB_WOC['OPERAND'], array('RDREC', 'WRREC'))) {
                    $_OPTB_WOC['recordType'] = 'M';
                    $modificationContent .= 'M' . '^' . sprintf("%06X", hexdec($_OPTB_WOC['LOC']) + 1) . '^' . '05' . "\r\n";
                }
                //var_dump($Tlength);
                array_push($OPTB_WOC_OP, $_OPTB_WOC);
            }

            $textContent .= 'T' . '^' . $TstartinggAdd . '^' . sprintf("%02X", $Tlength) . '^' . substr($Ttemp, 0, -1) . "\r\n";
            $Tlength = 0;
            $TstartinggAdd = '00' . $_OPTB_WOC['LOC'];
            $Ttemp = $_OPTB_WOC['OBJECTCODE'];
            // $Ttemp =  sprintf("%06X", hexdec($_OPTB_WOC['OBJECTCODE'])) . '^' ;
            $writed = true;



            // first if scope fix bug-_- . don't modify dont ask dont touch
            if(max(array_column($this->OPTB_WOC, 'BLOCK')) > 0 ) {
                $endingAddress1 = max(array_column($this->OPTB_WOC, 'ELOC'));
                $endingAddress2 = max(array_column($this->OPTB_WOC, 'realLOC'));
                $endingAddress = ($endingAddress1 > $endingAddress2) ?  $endingAddress1 : $endingAddress2;
            }else{
                $endingAddress = 0; // initial  only .. just bullshit..
                foreach (array_reverse(array_column($this->OPTB_WOC, 'ELOC')) as $lastELOC) {
                    if (!empty($lastELOC)) {
                        echo "lastELOC : " . $lastELOC . "\r\n";
                        $endingAddress = $lastELOC;
                        break;
                    }
                }
            }


            $programLength = hexdec($endingAddress) - hexdec($startingAddress);

            if(empty($programName)) $programName = 'COPY';
            $programLength = sprintf("%06X", ($programLength));

            $headerContent = 'H' . sprintf('%-6s', $programName) . '^'. $startingAddress . '^'.  $programLength;

            $endContent = $startingAddress;

            $headerRecord       = $headerContent . "\r\n";
            $defineRecord       = $defineContent . "\r\n";
            $referRecord        = $referContent . "\r\n";
            $textTrecord        = $textContent;                // contain new line dont worry
            $modificationRecord = $modificationContent; // contain new line dont worry
            $endRecord          = 'E'.'^'.$endContent. "\r\n";



            if(empty($headerContent)) $headerRecord = '';
            if(empty($defineContent)) $defineRecord = '';
            if(empty($referContent)) $referRecord = '';
            if(empty($textContent)) $textTrecord = '';
            if(empty($modificationContent)) $modificationRecord = '';

            // fix lastline T^00001D^00^ problem .. delete it
            $fixTextTrecord = explode("\r\n", $textTrecord);
            //print_r($fixTextTrecord);
            end($fixTextTrecord);
            $tRecordLastIndex = key($fixTextTrecord);
            if(empty($fixTextTrecord[$tRecordLastIndex])) $tRecordLastIndex = $tRecordLastIndex - 1;
            if( substr($fixTextTrecord[$tRecordLastIndex],-4) == '^00^') {
                unset($fixTextTrecord[$tRecordLastIndex]);
            }
            $textTrecord = implode("\r\n", $fixTextTrecord);


            $this->objectProgramContent = $headerRecord . $defineRecord . $referRecord .$textTrecord . $modificationRecord . $endRecord;

            echo $this->objectProgramContent;
        }
        else {
            $objectProgram = array();
            $countSize = 0;
            $tLine = 0;
            $previousSection = 'no_value';
            $index = 0;
            $modificationArr = array();
            $modificationSortedArr = array();
            $storageTypeArr = array('RESW' , 'RESB', 'BYTE', 'WORD');
            print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'fuckmeOPTB.html', $this->OPTB_WOC);
            $startLoc = sprintf("%04X", hexdec(array_search_value($this->OPTB_WOC, 'OPCODE', 'START', 'OPERAND')));
            foreach ($this->OPTB_WOC as $_OPTB) {
                if(!isset($_OPTB['SECTION']) || empty($_OPTB['SECTION'])) {
                    $_OPTB['SECTION'] = 0;
                }

                if($_OPTB['LINE'] == 5 || $_OPTB['OPCODE'] == 'START' || $_OPTB['OPCODE'] == 'CSECT') {
                    $objectProgram[$_OPTB['SECTION']]['NAME'] = $_OPTB['LABEL'];
                    //continue;
                }
                else if($_OPTB['OPCODE'] == 'EXTDEF') {
                    $extdef = explode(',', $_OPTB['OPERAND']);
                    //array_push($objectProgram[$_OPTB['SECTION']]['EXTDEF'], $extdef);
                    $objectProgram[$_OPTB['SECTION']]['EXTDEF'] = $extdef;
                    //continue;
                }
                else if($_OPTB['OPCODE'] == 'EXTREF') {
                    $extref = explode(',', $_OPTB['OPERAND']);
                    //array_push($objectProgram[$_OPTB['SECTION']]['EXTREF'], $extref);
                    $objectProgram[$_OPTB['SECTION']]['EXTREF'] = $extref;
                    //continue;
                }
                else if( isset($_OPTB['OBJECTCODE']) && !empty($_OPTB['OBJECTCODE'])) {
                    if(!isset($_OPTB_WOC)) {
                        $_OPTB['SIZE'] = strlen($_OPTB['OBJECTCODE']) / 2;
                    }
                    $countSize += $_OPTB['SIZE'];
                    if($previousSection != $_OPTB['SECTION'] ) {
                        $previousSection = $_OPTB['SECTION'];
                        $countSize = 0;
                        $tLine = 0;
                    }


                    if($countSize >= $textRecordMaxLen || ($index > 0 && $this->OPTB_WOC[$index -1]['OPCODE'] == 'LTORG')) {
                        ++$tLine;
                        $countSize = 0;
                    }

                    $objectProgram[$_OPTB['SECTION']]['TEXT'][] =
                        array(  'LOC'       => $_OPTB['LOC'],
                                'OBJECTCODE'=> $_OPTB['OBJECTCODE'],
                                'SIZE'      => strlen($_OPTB['OBJECTCODE']) / 2,
                                'TLINE'     => $tLine,
                                'INDEX'     => $index,
                            );

                    //continue;
                }
                ++$index;
            } // end foreach

            $previousTLINE = 'no_vlaue';
            $opIndex = 0;
            $tIndex = 0;
            foreach($objectProgram as $_objectProgram) {
                foreach($_objectProgram['TEXT'] as $_TEXT) {

                        if(!isset($objectProgram[$opIndex]['TRECORD'][$_TEXT['TLINE']])) {
                            $objectProgram[$opIndex]['TRECORD'][$_TEXT['TLINE']]['STARTLOC'] = $_TEXT['LOC'];
                        }
                        if(!isset( $objectProgram[$opIndex]['TRECORD'][$_TEXT['TLINE']]['LENGTH'] )) {
                            $objectProgram[$opIndex]['TRECORD'][$_TEXT['TLINE']]['LENGTH'] = 0;
                        }
                        $objectProgram[$opIndex]['TRECORD'][$_TEXT['TLINE']]['LENGTH'] += $_TEXT['SIZE'];
                }
                ++$opIndex;
                $previousTLINE = 'no_vlaue';
            } // end foreach

            // fix some unique bug literal loc missing // only for this figure only= =..
            $index = 0;
            $lastELOC = 'no_value';
            foreach($this->OPTB_WOC as $_OPTB) {

                if(isset($_OPTB['LABEL'])) {
                    if(!empty($_OPTB['ELOC'])) {
                        $lastELOC = $_OPTB['ELOC'];
                    }

                    if($_OPTB['LABEL'] == '*') {

                        if($_OPTB['OPCODE']{1} == 'C') {
                            $size = strlen(get_string_between($_OPTB['OPCODE'], "'", "'")) ;
                        }

                        else if($_OPTB['OPCODE']{1} == 'X') {
                            $size = strlen(get_string_between($_OPTB['OPCODE'], "'", "'")) / 2 ;
                            if($size == 0.5) {
                                $size = 1;
                            }
                        }
                        $this->OPTB_WOC[$index]['LOC'] = $lastELOC;
                        $this->OPTB_WOC[$index]['SIZE'] = $size;
                        $newELOC = hexdec($lastELOC) + $size ;
                        $this->OPTB_WOC[$index]['ELOC'] = sprintf("%04X" , $newELOC);
                        $lastELOC = $this->OPTB_WOC[$index]['ELOC'];
                    }
                }
                ++$index;
            } // end foreach
            print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'fixedOPTB.html', $this->OPTB_WOC);

            $maxSection = max(array_column($this->OPTB_WOC, 'SECTION'));
            for($i = 0; $i <= $maxSection; $i++) {
                $tmpOPTB = $this->optb_woc_filter('SECTION', $i);
                $maxLoc = max(array_column($tmpOPTB, 'ELOC'));
                //$minLoc = min(array_column($tmpOPTB, 'LOC'));
                $minLoc = array_column($tmpOPTB, 'LOC')[0];
                $objectProgram[$i]['LENGTH'] = hexdec($maxLoc) - hexdec($minLoc);
                $objectProgram[$i]['LENGTH'] = sprintf('%06X', $objectProgram[$i]['LENGTH']);
                $objectProgram[$i]['STARTLOC'] = $minLoc;
                $objectProgram[$i]['ENDLOC'] = $maxLoc;
            }

            print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'objectprogramArray.html', $objectProgram);
            
            $content = '';
            $delimiter = '^';
            $objectProgramRes = array();

            for($index = 0; $index <  count($objectProgram); $index++) {
                // Header record :
                $objectProgramRes[$index] = 'H'. $delimiter. sprintf("%-6s", $objectProgram[$index]['NAME']) .
                    $delimiter . sprintf("%06X", hexdec($objectProgram[$index]['STARTLOC'])) .
                    $delimiter . sprintf("%06X", hexdec($objectProgram[$index]['ENDLOC'])) .
                    "\r\n";

                // Define record :
                $dRecord = '';  // initial as empty string
                if(!empty($objectProgram[$index]['EXTDEF'])) {
                    $dRecord = 'D';
                    for($e = 0; $e < count($objectProgram[$index]['EXTDEF']); $e++ ) {
                        $defineLoc = $this->define_record_search_loc($objectProgram[$index]['EXTDEF'][$e], $index);
                        $defineSymbol = sprintf("%-6s", $objectProgram[$index]['EXTDEF'][$e]);
                        $dRecord .= $delimiter . $defineSymbol .
                                    $delimiter . sprintf("%06X", hexdec($defineLoc));
                    }
                    $objectProgramRes[$index] .= $dRecord . "\r\n";
                } // end if extdef !empty

                // Refer record :
                $rRecord = '';  // initial as empty string
                if(!empty($objectProgram[$index]['EXTREF'])) {
                    $rRecord = 'R';
                    for($e = 0; $e < count($objectProgram[$index]['EXTREF']); $e++ ) {
                        $defineSymbol = sprintf("%-6s", $objectProgram[$index]['EXTREF'][$e]);
                        $rRecord .= $delimiter . $defineSymbol;
                    }
                    $objectProgramRes[$index] .= $rRecord . "\r\n";
                } // end if extref !empty


                // Text record :
                $tRecord = ''; // initial as empty string
                $tRecordLine = NULL;
                if(isset($objectProgram[$index]['TEXT']) && !empty($objectProgram[$index]['TEXT'])) {
                    for($t = 0; $t < count($objectProgram[$index]['TRECORD']); $t++) {

                        $startLocTmp = sprintf("%06X", hexdec($objectProgram[$index]['TRECORD'][$t]['STARTLOC']));
                        $lengthTmp = sprintf("%02X", $objectProgram[$index]['TRECORD'][$t]['LENGTH']);
                        $tRecordLine[$t] = 'T' . $delimiter . $startLocTmp . $delimiter . $lengthTmp;

                        for($t2 = 0; $t2 < count($objectProgram[$index]['TEXT']); $t2++) {
                            if($objectProgram[$index]['TEXT'][$t2]['TLINE'] == $t) {
                                $tRecordLine[$t] .= $delimiter . $objectProgram[$index]['TEXT'][$t2]['OBJECTCODE'];
                            }
                            else if($objectProgram[$index]['TEXT'][$t2]['TLINE'] < $t)
                                continue;
                            else if($objectProgram[$index]['TEXT'][$t2]['TLINE'] > $t)
                                break;
                        } // end inner for
                    } // end for
                    print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'tRecordLine'.$index.'.html', $tRecordLine);
                    $tRecord = implode("\r\n", $tRecordLine);
                    $objectProgramRes[$index] .= $tRecord . "\r\n";
                } // end if text record !empty

                // Modification record (revised) :
                $mRecord = '';
                if(isset($objectProgram[$index]['EXTREF']) && !empty($objectProgram[$index]['EXTREF'])) {
                        foreach($objectProgram[$index]['EXTREF'] as $extref) {
                            if(!empty($this->modification_record($extref, $index))) {
                                $modificationArr[$index][] = $this->modification_record($extref, $index);
                            } // end if
                        } // end forach

                        // originating array ..
                        foreach($modificationArr[$index] as $_extref) {
                            if(is_array($_extref)) {
                                foreach($_extref as $__extref) {
                                    $modificationSortedArr[$index][] = $__extref;
                                } // end inner foreach
                            } // end is_array
                        } // end foreach

                        // sorting array :
                        $decloc = array();
                        foreach ($modificationSortedArr[$index] as $key => $row) {
                            $decloc[$key] = $row['DECLOC'];
                        }
                        array_multisort($decloc, SORT_ASC,  $modificationSortedArr[$index]);

                        $mRecord = '';
                        foreach ($modificationSortedArr[$index] as $_extref) {
                            $mRecord .= 'M' . $delimiter . $_extref['LOC'] . $delimiter . $_extref['SIZE'] .
                                        $delimiter .  $_extref['OPERATOR'] . $_extref['SYMBOL'] . "\r\n";
                        }

                    $objectProgramRes[$index] .= $mRecord;
                    //$objectProgramRes[$index] .= $mRecord . "\r\n";
                } // end if extref record !empty

                // End record :
                $eRecord = '';
                if(isset($objectProgram[$index]['STARTLOC']) && !empty($objectProgram[$index]['STARTLOC'])) {
                    $eRecord .= 'E';
                    if($index == 0) {
                        $eRecord .= $delimiter . $objectProgram[$index]['STARTLOC'];
                    }
                    $objectProgramRes[$index] .= $eRecord . "\r\n";
                }



                // combine to file content ...
                $content .= $objectProgramRes[$index] . "\r\n\r\n\r\n";
            } // end for loop

            echo $content;
            $this->objectProgramContent = $content;
            
            print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'modificationSortedArr'.$index.'.html', $modificationSortedArr);
            print_r_to_html(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'modificationArr'.$index.'.html', $modificationArr);
            //create_file(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'object-program.txt',$content);
            //file_put_contents(RESULT_PATH.'/'.$OUTPUT_LOG_PREFIX.'object-program.txt',$content);
            
            //print_r($objectProgram);
        } // end else

       // exit;
    }

    private function modification_record($extref = '', $section = 0) {
        $array = array();   // array('LOC' => $_OPTB['LOC'],
        $extra = false;
        $index = 0;
        $type = 'STORAGE';
        $storageTypeArr = array('RESW' , 'RESB', 'BYTE', 'WORD');
        $operatorArr = array('+','-','*','/');
        foreach($this->OPTB_WOC as $_OPTB) {
            //
            if( //isset($_OPTB['LABEL']) && !empty($_OPTB['LABEL']) &&
                !empty($_OPTB['LOC']) && isset($_OPTB['LOC']) &&
                 $_OPTB['SECTION'] == $section && strposa($_OPTB['OPERAND'], $extref)) {

                $pos = strpos($_OPTB['OPERAND'], $extref);
                if ($pos == 0)
                    $operator = '+';
                else if (in_array($_OPTB['OPERAND']{$pos - 1}, $operatorArr))
                    $operator = $_OPTB['OPERAND']{$pos - 1};
                else if ($_OPTB['OPERAND']{$pos} == '@' || $_OPTB['OPERAND']{$pos} == '#') {
                    $operator = '+';
                }

                if(count(preg_split("/[+\/*-]/",  $_OPTB['OPERAND'])) > 1) {
                    $size = '06';
                }else {
                    $size = '05';
                }

                if (!in_array($_OPTB['OPCODE'], $storageTypeArr)) {
                    $_OPTB['LOC'] = hexdec($_OPTB['LOC']) + 1;
                    $_OPTB['LOC'] = sprintf("%04X", $_OPTB['LOC']);
                    $type = 'OPCODE';
                }

                //array_push($array, array('SYMBOL' => $extref, 'LOC' => $_OPTB['LOC'], 'EXTRA' => $extra, 'operator' => $operator));
                $arrayTmp = array('SYMBOL'  => $extref,
                                'SECTION'   => $section,
                                'LOC'       => sprintf("%06X", hexdec($_OPTB['LOC'])),
                                'DECLOC'    =>  hexdec($_OPTB['LOC']),
                                'TYPE'      => $type, 'EXTRA' => $extra,
                                'SIZE'      => $size,
                                'OPERATOR'  => $operator, 'INDEX' => $index);
                array_push($array, $arrayTmp);
            }
            ++$index;
        }
        return $array;
    }



    private function define_record_search_loc($label = '', $section = 0) {
        foreach($this->OPTB_WOC as $_OPTB) {
            if(isset($_OPTB['LABEL']) && !empty($_OPTB['LABEL'])) {
                if($_OPTB['LABEL'] == $label && $_OPTB['SECTION'] == $section) {
                    return $_OPTB['LOC'];
                }
            }
        } // end foreach
        echo 'Relate location could not found.' . PHP_EOL;
        return 9999;
    }

    private function optb_woc_filter($key = 'SECTION' , $value = 0) {
        $OPTB = array();
        foreach($this->OPTB_WOC as $_OPTB) {
            // debug.. fix bug.. .. just for safty reason.. main reason. I don't how my code process..
            if(!isset($_OPTB[$key]))
                continue;
            else if($_OPTB[$key] == $value) {
                array_push($OPTB, $_OPTB);
            }
        } // end foreach
        return $OPTB;
    }

    public function writeTxt($fileName) {
        file_put_contents($fileName, $this->objectProgramContent);
    }


    function __destruct() {
        echo "Destroying " . $this->name. "\n";
    }
}