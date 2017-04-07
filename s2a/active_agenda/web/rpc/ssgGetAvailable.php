<?php
/**
 * Handles XMLHttpRequest messages (AJAX) from SearchSelectGrids
 *
 * Returns a list of items (usu. people) matching the submotted search criteria
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
require_once '../../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//necessary for unserializing
include_once CLASSES_PATH . '/search.class.php';
include_once CLASSES_PATH . '/components.php';

//causes session timeouts to return a catchable response
define('IS_RPC', true);

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

//search fields of the module, serialized in this file
$filename = GENERATED_PATH . '/'.$ModuleID.'/'.$ModuleID.'_SearchFields.gen';

//check for cached page for this module
if (!file_exists($filename)){
    die("ERROR MESSAGE (ssgGetAvailable.php): Could not find file '$filename'.");
}

//the included file extracts $searchFields
include $filename;

trace($_POST, "ssg POST");
trace(array_keys($searchFields), "ssg searchFields");

// create a new instance of JSON
require_once THIRD_PARTY_PATH.'/JSON.php';
$json = new JSON();

$search = $_SESSION['Search_ssg_'.$ModuleID];
if(empty($search)){
    $search = new Search($ModuleID);
}

$search->prepareFroms(&$searchFields, &$_POST);

$SQL = $search->getListSQL('Name');

$available = $dbh->getAssoc($SQL);

print $json->encode($available);
?>


