<?php
/**
 * Handles XMLHttpRequest messages (AJAX) to display dashboard grids
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

//causes session timeouts to return a catchable response
define('IS_RPC', true);

//yes we need all of these unfortunately
require_once CLASSES_PATH . '/grids.php'; //for field classes, (A)EditGrid class
include_once CLASSES_PATH . '/modulefields.php'; //for RemoteField class only

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

include $theme .'/component_html.php';


// create a new instance of JSON
require_once THIRD_PARTY_PATH . '/JSON.php'; //use PEAR package when released
$json = new JSON();

include_once INCLUDE_PATH.'/dashboardGrids.php';
include_once GENERATED_PATH.'/dsb/dsb_'.$ModuleID.'DashboardGrid.gen'; //returns $dashgrid

$content = $dashgrid->render('home.php', array());
$countSQL = $dashgrid->prepareCountSQL();
$count = $dbh->getOne($countSQL);
dbErrorCheck($count);

$response = array();
$response['rowcount'] = intval($count);
$response['content'] = $content;
echo $json->encode($response);
?>