#!/usr/bin/php
<?php
/**
 * Utility to export a user's charts as images
 * 
 * PHP version 5
 *
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
 * @author         Mattias Thorslund <mthorslund@activeagenda.net>
 * @copyright      2003-2009 Active Agenda Inc.
 * @license        http://www.activeagenda.net/license  RPL 1.5
 * @version        SVN: $Revision: 871 $
 * @last-modified  SVN: $Date: 2007-06-19 16:25:33 -0700 (Tue, 19 Jun 2007) $
 */

$printHelp = false;
$errMessage = '';
if(empty($_SERVER['argc'])){
    $printHelp = true;
    $errMessage = 'Your PHP executable appears not to support command-line parameters. You may want to look for info on "php-cli" in the PHP documentation online.';
} elseif (2 > $_SERVER['argc']){
    $printHelp = true;
    $errMessage = 'Not enough parameters.';
}

if($printHelp){
    print '
    s2a-test-function: executes an Active Agenda function and prints the return value.

    BASIC USAGE:
    php cron-export-charts.php <userName> [<height> [<width> [<verbose>]]]

    PARAMETERS:
    <userName>  Must be a valid user (required)
    <height>    Height of chart images, in pixels (default: 200)
    <width>     Width of chart images, in pixels. If omitted, this defaults
                to 1.5 times <height>, i.e. 300 pixels by default)
    <verbose>   [yes/no] Whether to print anything upon if there are no errors.
                (default: no)
    ';
    print "$errMessage\n";
    die();
}

$UserName   = $_SERVER['argv'][1];
$Height = 200;
if (2 < $_SERVER['argc']){
    $Height = $_SERVER['argv'][2];
}
$Width = $Height * 1.5;
if (3 < $_SERVER['argc']){
    $Width = $_SERVER['argv'][3];
}
$debug = false;
if (4 < $_SERVER['argc']){
    $debug = 'yes' == strtolower(trim($_SERVER['argv'][4]));
}

$Project = 'active_agenda';

//assumes this script is in the 's2a/lib/cron' folder 
$site_folder = realpath(dirname(__FILE__).'/../..');
$site_folder .= '/'.$Project;

//includes
$config_file = $site_folder . '/config.php';
if(!file_exists($config_file)){
    print "Config file not found at $config_file\n";
    exit;
}

//get settings
include_once $config_file;
//include_once $gen_config_file;

set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

require_once PEAR_PATH . '/DB.php' ;  //PEAR DB class

//utility functions
include_once INCLUDE_PATH . '/parse_util.php';
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/web_util.php';

require_once CLASSES_PATH . '/search.class.php';
require_once CLASSES_PATH . '/user.class.php';

/**
 * Sets custom error handler
 */
set_error_handler('handleError');

global $dbh;
$dbh = DB::connect(DB_DSN);
dbErrorCheck($dbh);


/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
DEFINE('EXEC_STATE', 2);

//get user:
$User = User::Masquerade($UserName);

//print_r($User);

$SQL = "SELECT
    dsbc.DashboardChartID,
    modch.Title,
    dsbc.ModuleID,
    dsbc.ChartName,
    dsbc.ConditionPhrases
FROM dsbc
    INNER JOIN modch
    ON dsbc.ChartName = modch.Name AND dsbc.ModuleID = modch.ModuleID
WHERE dsbc.UserID = {$User->PersonID}
    AND dsbc._Deleted = 0
ORDER BY dsbc.SortOrder";

$r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
$uniqueDashCharts = array();

$title = false;
$chartInfo = array(); //collects the data for the HTML, XML info

if(count($r) > 0){
    foreach($r as $row){
        if($debug){
            print "Chart: {$row['ModuleID']} {$row['ChartName']} {$row['Title']}\n   Conditions: {$row['ConditionPhrases']}\n\n";
        }
        $cachedChartLocation = GENERATED_PATH ."/{$row['ModuleID']}/{$row['ModuleID']}_{$row['ChartName']}_Chart.gen";
        $chartID = "{$row['ModuleID']}_{$row['ChartName']}";

        if(!array_key_exists($chartID, $uniqueDashCharts)){
            include_once($cachedChartLocation); //returns $chart
            $uniqueDashCharts[$chartID] =& $chart;
            unset($chart);
        }

        $filePath = CHART_EXPORT_LOCATION.'/'.$UserName.'_'.$chartID.'_'.$row['DashboardChartID'].'.png';

        $uniqueDashCharts[$chartID]->dashboardChartID = $row['DashboardChartID'];
        $uniqueDashCharts[$chartID]->fileName = $filePath;
        $uniqueDashCharts[$chartID]->render($Width, $Height, $title);

        $chartInfo[$filePath] = array(
            'DashboardChartID' => $row['DashboardChartID'],
            'Title'            => $row['Title'],
            'ChartName'        => $row['ChartName'],
            'ConditionPhrases' => $row['ConditionPhrases']
        );
    }
}

$content = '<ChartInfo timestamp="'.date('Y-m-d H:i:s').'" username="'.$UserName.'">'."\n";
foreach($chartInfo as $filePath => $info){
    $fileName = basename($filePath);
    $content .= '   <Chart id="'.$info['DashboardChartID'].'" fileName="'.$fileName.'" chartName="'.$info['ChartName'].'" title="'.$info['Title'].'">';
    $content .= $info['ConditionPhrases'];
    $content .= '</Chart>'."\n";
}
$content .= '</ChartInfo>';

//write the new file to the disk
$fileName = $UserName.'_chartInfo.xml';
saveFile(CHART_EXPORT_LOCATION.'/'.$fileName, $content, $debug);

//defines the HTML 
include_once(CHART_EXPORT_TEMPLATE);

$content = '';
foreach($chartInfo as $filePath => $info){
    $fileName = basename($filePath);
    $content .= sprintf(
        CHART_EXPORT_TRIM,
        $info['DashboardChartID'],
        $info['Title'],
        $info['ConditionPhrases'],
        $fileName);
}

$content = sprintf(CHART_EXPORT_EMBED, CHART_EXPORT_TITLE, $content, $debug);
$fileName = $UserName.'_charts_embed.html';
saveFile(CHART_EXPORT_LOCATION.'/'.$fileName, $content, $debug);

$content = sprintf(CHART_EXPORT_PAGE, CHART_EXPORT_TITLE, $content, $debug);
$fileName = $UserName.'_charts.html';
saveFile(CHART_EXPORT_LOCATION.'/'.$fileName, $content, $debug);


function saveFile($file_path, $file_content, $debug = false)
{
    if($fp = fopen($file_path, 'w')) {
        if(fwrite($fp, $file_content)){
            if($debug){
                print "File $file_path saved.\n";
            }
        } else {
            trigger_error("Could not create file $file_path.", E_USER_ERROR );
        }
        fclose($fp);
    }
    else {
        trigger_error("Unable to write file $file_path.", E_USER_ERROR );
    }
}

?>
