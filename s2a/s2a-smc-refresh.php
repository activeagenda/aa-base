<?php
/**
 * Utility to refresh cached data in the SubModule Cache
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
 * @version        SVN: $Revision: 1545 $
 * @last-modified  SVN: $Date: 2009-03-04 19:23:38 +0100 (Śr, 04 mar 2009) $
 */

$Project = $_SERVER['argv'][1];

/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
DEFINE('EXEC_STATE', 2);

if(empty($Project)){
    print '
s2a-smc-refresh: refreshes the SubModule Cache for all modules.

     USAGE:
     ./s2a-smc-refresh.php <project_name>
';
    die('Not enough parameters.');
}

print "s2a-smc-refresh: project = $Project\n";

//assumes we're in the 's2a' folder 
$site_folder = realpath(dirname($_SERVER['SCRIPT_FILENAME']).'');
$site_folder .= '/'.$Project;

//includes
$config_file = $site_folder . '/config.php';
if(!file_exists($config_file)){
    print "Config file not found at $config_file\n";
    exit;
}
$gen_config_file = $site_folder . '/gen-config.php';
if(!file_exists($gen_config_file)){
    print "Config file not found at $gen_config_file\n";
    exit;
}

//get settings
include_once $config_file;
include_once $gen_config_file;
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//classes
include_once CLASSES_PATH . '/data_handler.class.php';
//include_once CLASSES_PATH . '/module.class.php';
include_once INCLUDE_PATH . '/general_util.php';
//include_once INCLUDE_PATH . '/parse_util.php';

//connect to database
require_once PEAR_PATH . '/DB.php' ;  //PEAR DB class

//connect with superuser privileges - regular user has no permission to
//change table structure
global $dbh;
$dbh = DB::connect(GEN_DB_DSN);
dbErrorCheck($dbh);

$moduleParseList = array();
global $moduleParseList;

print "s2a-smc-refresh: we'll update submodule caches for all modules\n";

$SQL = "SELECT ModuleID FROM `mod` ORDER BY ModuleID;\n";

//get data
$r = $dbh->getCol($SQL);
dbErrorCheck($r);

$moduleList = array_flip($r);

//empty all smc data:
print "purging all SMC records\n";
$SQL = "TRUNCATE TABLE `smc`";
$result = $dbh->query($SQL);
dbErrorCheck($result);

//print_r($moduleParseList);
foreach($moduleList as $moduleID => $dummy){
    if(!in_array($moduleID, array('smc', 'rdc', 'modd', 'spts', 'cod', 'codtd', 'trx', 'trxr', 'usrp'))){
        print "refreshing SMC for $moduleID\n";
        $moduleInfo = GetModuleInfo($moduleID);

        $PrimaryKeys = $moduleInfo->getProperty('primaryKeys');
        $PrimaryKeyFields = join(', ', $PrimaryKeys);
        $SQL = "SELECT $PrimaryKeyFields FROM `$moduleID`";

        $records = $dbh->getAll($SQL);
        //print_r($records);

        if(count($records) > 0){
            $dh = GetDataHandler($moduleID);
            foreach($records as $fieldValues){
                foreach($PrimaryKeys as $pkID => $pk){
                    $dh->PKFieldValues[$pk] = $fieldValues[$pkID];
                }
                $recordID = end($dh->PKFieldValues);
                $PKField = end($dh->PKFields);
                $dh->_saveSMC($recordID, $PKField);
            }

            $SQL = "SELECT count(*) FROM `smc` WHERE SubModuleID = '$moduleID'";
            $result = $dbh->getOne($SQL);
            dbErrorCheck($result);
            print "Number of cache records for submodule $moduleID: $result\n";
        } else {
            print "No records for module $moduleID\n";
        }
    }
}
?>