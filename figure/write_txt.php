<?php


/**
 * @param $filename
 * @param $array
 * @return mixed
 */



function write_txt($filename, $array) {
    if( do_overwrite($filename,false) == !false) {
        $content = "";
        foreach($array as $_OPTB_WOC) {
            $content .=  $_OPTB_WOC['LABEL']
                . "\t" . $_OPTB_WOC['OPCODE'] . "\t" . $_OPTB_WOC['OPERAND'];
            $content .= "\r\n";
        }
        file_put_contents($filename, $content);
    }
    return;
}

?>