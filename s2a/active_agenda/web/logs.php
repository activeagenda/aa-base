<?
/**
 * A way to display the latest log messages
 *
 * LICENSE NOTE:
 *
 * Copyright  2003-2009 Active Agenda Inc., All Rights Reserved.
 *
 * Unless explicitly acquired and licensed from Licensor under another license, the
 * contents of this file are subject to the Reciprocal Public License ("RPL")
 * Version 1.5, or subsequent versions as allowed by the RPL, and You may not copy
 * or use this file in either source code or executable form, except in compliance
 * with the terms and conditions of the RPL. You may obtain a copy of the RPL from
 * Active Agenda Inc. at http://www.activeagenda.net/license.
 *
 * All software distributed under the RPL is provided strictly on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, AND LICENSOR HEREBY
 * DISCLAIMS ALL SUCH WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT, OR
 * NON-INFRINGEMENT. See the RPL for specific language governing rights and
 * limitations under the RPL.
 *
 * author         Mattias Thorslund <mthorslund@activeagenda.net>
 * copyright      2003-2009 Active Agenda Inc.
 * license        http://www.activeagenda.net/license
 **/

//general settings
require_once '../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';
include_once $theme .'/component_html.php';
include_once INCLUDE_PATH . '/general_util.php';

$message = '';
$content = '';

if(!$User->IsAdmin){
    $message = gettext("You must be an Administrator to read the log file.");
}

$errorFile = GEN_LOG_PATH . '/errors.log';
if(empty($message) && !file_exists($errorFile)){
    $message = gettext("The log file does not exist.");
}

$size = filesize($errorFile);
if(empty($message) && 0 == $size){
    $message = gettext("The log file is empty.");
}

if(empty($message) && !$fp = fopen($errorFile, 'r')){
    $message = gettext("The log file cannot be read.");
}

//trigger_error("test error", E_USER_WARNING);

if(empty($message)){
    $buffer = '';
    $target_string = '- - - - -';
    $target_len = strlen($target_string);
    //print "\$target_len $target_len <br />";

    $n_show_messages = 10;
    if(!empty($_GET['n'])){
        if(intval($_GET['n']) > 0){
            $n_show_messages = intval($_GET['n']);
        }
    }

    $message_ix = -1;
    $message_separators = array();

    $offset = $size -$target_len;
    if(0 != fseek($fp, $offset)){
        $message = gettext("Problem reading the file.");
    }
    $offset--;
    $buffer = fread($fp, $target_len);
    fseek($fp, $offset);

    while((false !== $char = fread($fp, 1)) && $offset >= 0){
        $offset--;
        fseek($fp, -2, SEEK_CUR);
        $buffer = $char . substr($buffer, 0, -1);

        if($buffer == $target_string){
            //print "<br />\$found $buffer at $offset<br />";
            $message_separators[] = $offset;
            if($message_ix == $n_show_messages){
                $content .= '<h1>'. sprintf(gettext("Showing %s latest messages:"), $message_ix)."</h1>\n";
                break;
            }
            $message_ix++;
        }

    }

    array_shift($message_separators);
    //print debug_r($message_separators);

    $message_start_separator = array_shift($message_separators);
    foreach($message_separators as $message_end_separator){
        fseek($fp, $message_start_separator + $target_len +1);
        $errmsg = fread($fp, $message_start_separator - $message_end_separator - $target_len);
        $content .= "<p>";
        $content .= debug_r($errmsg);
        $content .= "</p>\n";
        $content .= "<hr/>\n";
        $message_start_separator = $message_end_separator;
    }
    fclose($fp);
} else {
    $content .= "$message <br />";
}


$title = gettext("Error Log Viewer");
include_once $theme . '/no-tabs.template.php';
?>