<?php
/**
 * Handles XMLHttpRequest messages (AJAX) for manipulating dashboard charts
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

//main include file - performs all general application setup
require_once INCLUDE_PATH . '/page_startup.php';

include $theme .'/component_html.php';


// create a new instance of JSON
require_once THIRD_PARTY_PATH . '/JSON.php'; //use PEAR package when released
$json = new JSON();
$error = '';
$content = '';

$dsbcID = intval($_GET['dsbc']);

$r = $dbh->query('BEGIN');
dbErrorCheck($r);

$SQL = "SELECT ChartName, SortOrder from dsbc WHERE UserID = '{$User->PersonID}' AND DashboardChartID = '$dsbcID'";

$row = $dbh->getRow($SQL, DB_FETCHMODE_ASSOC);
dbErrorCheck($row);


if(empty($row)){
    $error .= $SQL;
    $error .= gettext("Invalid dashboard chart.")."\n";
} else {
    switch($_GET['action']){
    case 'up':
        $action = 'up';

        //chart to swap SortOrder with
        $moveSQL = "SELECT DashboardChartID, SortOrder FROM dsbc WHERE _Deleted = 0 AND UserID = '{$User->PersonID}' AND SortOrder < '{$row['SortOrder']}' ORDER BY SortOrder DESC LIMIT 1";

        break;
    case 'dn':
        $action = 'down';

        //chart to swap SortOrder with
        $moveSQL = "SELECT DashboardChartID, SortOrder FROM dsbc WHERE _Deleted = 0 AND UserID = '{$User->PersonID}' AND SortOrder > '{$row['SortOrder']}' ORDER BY SortOrder ASC LIMIT 1";

        break;
    case 'rm':
        $action = 'remove';
        break;
    default:
        $error .= gettext("Unknown action requested")."\n";
    }

    if(empty($error)){
        if($action == 'remove'){
            $SQL = "UPDATE dsbc SET _Deleted = 1 WHERE DashboardChartID = '$dsbcID'";
            //echo "'$SQL'<br />\n";
            $r = $dbh->query($SQL);
            dbErrorCheck($r);
            $content = 'success';
        } else {
            $row_swap = $dbh->getRow($moveSQL, DB_FETCHMODE_ASSOC);
            dbErrorCheck($row_swap);

            if(!empty($row_swap)){

                //update the "swapped" chart
                $SQL = "UPDATE dsbc SET SortOrder = {$row['SortOrder']} WHERE DashboardChartID = '{$row_swap['DashboardChartID']}'";
                $r = $dbh->query($SQL);
                dbErrorCheck($r);

                //update the "moved" char
                $SQL = "UPDATE dsbc SET SortOrder = {$row_swap['SortOrder']} WHERE DashboardChartID = '$dsbcID'";
                $r = $dbh->query($SQL);
                dbErrorCheck($r);
                $content = $row_swap['SortOrder']; //return the new sortorder
            } else {
                echo "";
            }
        }
    }
}



$r = $dbh->query('COMMIT');
dbErrorCheck($r);

$response = array();
//$response['rowcount'] = intval($count);
$response['action'] = $action;
$response['content'] = $content;
$response['error'] = $error;
echo $json->encode($response);
?>