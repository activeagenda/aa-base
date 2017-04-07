<?php
/**
 * Checks some configurations on the server.
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

if($User->IsAdmin){
	$content = '<h1>'.gettext("Checking file permissions on the server:")."</h1>\n";
	$content .= gettext("Note: This is a rudimentary check for the purpose of ensuring basic functionality, not security. A more comprehensive server configuration check might be implemented in a later release of Active Agenda."). '<br /> ';

	$content .= '<h2>'.gettext("Checking file upload folder:"). '</h2>'.UPLOAD_PATH.'<br /> ';
	if(is_writeable(UPLOAD_PATH)){
		$content .= gettext("Folder is writeable (OK)");
	} else {
		$content .= '<b>'.gettext("Folder is NOT writeable.") .' ' .gettext("Warning: It is not possible to upload attachments.") . '</b>';
	}
	$content .= "<br /><br />\n";

	$content .= '<h2>'.gettext("Checking s2alog folder:"). '</h2>'.GEN_LOG_PATH.'<br /> ';
	if(is_writeable(GEN_LOG_PATH)){
		$content .= gettext("Folder is writeable (OK)");
		$file_location = GEN_LOG_PATH . '/errors.log';
		$content .= '<h2>'.gettext("Checking errors.log file:"). '</h2>'.$file_location.'<br /> ';
		if(file_exists($file_location)){
			$content .= gettext("File exists. Checking if it's writeable:")."<br />\n";
			if(is_writeable($file_location)){
				$content .= gettext("File is writeable (OK)")."<br />\n";
			} else {
				$content .= '<b>'.gettext("File is NOT writeable.") .' ' .gettext("Warning: Error messages cannot be logged. Additional error messages will be displayed to users. Be sure to change the permissions before using Active Agenda.") . '</b>';
			}
			
		} else {
			$content .= gettext("File does not exist, creating it...")."<br />\n";
			if(touch($file_location)){
				$content .= gettext("Created successfully!")."<br />\n";
			} else {
				$content .= gettext("Could not create file.")."<br />\n";
			}
		}

	} else {
		$content .= '<b>'.gettext("Folder is NOT writeable.") .' ' .gettext("Warning: Error messages cannot be logged. Additional error messages will be displayed to users. Be sure to change the permissions before using Active Agenda.") . '</b>';
	}
	$content .= "<br /><br />\n";

	

} else {
	$content = gettext("You are not allowed to access this page, because you are not a Site Administrator");
	trigger_error($content, E_USER_WARNING);
}

$title = gettext("Server check");

//$user_info;
$screenPhrase = ShortPhrase($screenPhrase);
$moduleID = $ModuleID;
//$recordID;
//$messages; //any error messages, acknowledgements etc.
//$content;

include_once $theme . '/popup.template.php';

?>