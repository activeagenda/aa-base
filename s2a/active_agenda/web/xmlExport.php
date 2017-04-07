<?php
/**
 * Returns XML data of a record
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

$moduleInfo = GetModuleInfo($ModuleID);

//if no record id, use the present search to display all the matching 
//get the record ID
$recordID = intval($_GET['rid']);
if($recordID == 0){
    if(strlen($_GET['rid']) >= 3){
        $recordID = substr($_GET['rid'], 0, 5);
    }
}
if($recordID == 0){
    $level = 'list';
    include_once CLASSES_PATH . '/search.class.php';
} else {
    $level = 'record';

}

$recordIDTags = array();
foreach($moduleInfo->getProperty('primaryKeys') as $pkField){
    $recordIDTags["[*$pkField*]"] = $recordID;
}


require_once CLASSES_PATH . '/report.class.php';
$includeFileName = GENERATED_PATH . "/{$ModuleID}/{$ModuleID}_Export.gen";
if(file_exists($includeFileName)){
    include_once $includeFileName;
} else {
    trigger_error("Could not find file $includeFileName.", E_USER_ERROR);
}


$exportFileName = '';//concatenate: module ID, record ID, date + time

$content = $exportReport->renderXML($recordID);
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="downloaded.xml"');
echo $content;
?>