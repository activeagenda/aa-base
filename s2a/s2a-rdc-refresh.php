<?php
/**
 * Utility to refresh cached data in the Record Description Cache
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
 * @version        SVN: $Revision: 1406 $
 * @last-modified  SVN: $Date: 2009-01-27 07:56:18 +0100 (Wt, 27 sty 2009) $
 */


/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
define('EXEC_STATE', 2);


if(!defined('PATH_SEPARATOR')){
    if(strtolower(substr(php_uname('s'), 0, 3)) == "win") {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}

//get PEAR class that handles command line arguments
//since this is needed before we include the config.php file, this is sort-of hard coded.
set_include_path('./pear' . PATH_SEPARATOR . get_include_path());
require_once 'pear/Console/Getargs.php';

$config = array();

$config['project'] =
    array('short' => 'p',
        'min'   => 0,
        'max'   => 1,
        'desc'  => 'The s2a project name. Must be a folder under the s2a folder.',
        'default' => 'active_agenda'
    );
$config['module'] =
    array('short'   => 'm',
        'min'     => 0,
        'max'     => 1,
        'desc'    => 'The module ID of the module which to refresh the Record Description Cache. If not specified, ALL modules will be selected.',
        'default' => 'optional'
    );
$config['remote_modules'] =
    array('short'   => 'r',
        'min'     => 0,
        'max'     => 1,
        'desc'    => 'Match remote modules only.',
        'default' => 'optional'
    );
$config['startat'] =
    array('short'   => 's',
        'min'     => 0,
        'max'     => 1,
        'desc'    => 'Within the matched module IDs, skip all modules prior to the module ID specified here.',
        'default' => 'optional'
    );
$config['help'] =
    array('short' => 'h',
        'max'   => 0,
        'desc'  => 'Show this help.'
    );

$args =& Console_Getargs::factory($config);
if (PEAR::isError($args)) {
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        // User put illegal values on the command line.
        echo Console_Getargs::getHelp($config, NULL, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        // User needs help.
        echo Console_Getargs::getHelp($config)."\n";
    }
    exit;
}

$Project        = $args->getValue('project');
$RemoteModules  = $args->getValue('remote_modules');
$moduleID       = $args->getValue('module');
$StartAt        = $args->getValue('startat');
if(empty($Project)){
    $Project = 'active_agenda';
}


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

//classes
include_once CLASSES_PATH . '/modulefields.php';
include_once CLASSES_PATH . '/module.class.php';
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/parse_util.php';

//connect to database
$mdb2 =& GetMDB2();

$moduleParseList = array();
global $moduleParseList;


if(!empty($moduleID)){
    //Insert/update caches for one module only
    UpdateRDCaches($moduleID);
    print "\n";
} else {
    if($RemoteModules) {
        print "s2a-rdc-refresh: we'll update caches for all remote modules\n";
        $limiter = ' AND mod.Remote = 1 ';
    } else {
        print "s2a-rdc-refresh: we'll update caches for all modules\n";
        $limiter = '';
    }

    //get module list from mod table
    $SQL = "SELECT ModuleID FROM `mod` WHERE 1=1 $limiter AND _Deleted = 0 ORDER BY ModuleID;\n";
    //get data
    $r = $mdb2->queryCol($SQL);
    mdb2ErrorCheck($r);

    $moduleParseList = array_flip($r);

    if(empty($StartAt)){
        $StartAt = reset(array_keys($moduleParseList));
    }
    $started = false;

    foreach($moduleParseList as $parseModule => $dummy){
        if($parseModule == $StartAt){
            $started = true;
        }
        if($started){
            print "updating $parseModule ... ";
            $result = UpdateRDCaches($parseModule);
            $moduleParseList[$parseModule] = $result;
            print "\n";
        } else {
            //print "skipped $parseModule";
            $moduleParseList[$parseModule] = 'skipped';
        }
    }

    //print_r ($moduleParseList);

}


function UpdateRDCaches($moduleID){
    $mdb2 =& GetMDB2();
    $mfs = GetModuleFields($moduleID);
    $moduleInfo = GetModuleInfo($moduleID);

    $RDFieldName = $moduleInfo->getProperty('recordDescriptionField');

    if(empty($RDFieldName)){
        $RDFieldName = 'RecordDescription';
    }

    if($RDFieldName){
        if(!array_key_exists($RDFieldName, $mfs)){
            print "no RecordDescription field";
            return 'RD field not in ModuleFields';
        }

        /************************ 
            INITIALIZE
        ************************/

        $pkField = reset($mfs);
        $rdField = $mfs[$RDFieldName];

        global $SQLBaseModuleID;
        $SQLBaseModuleID = $moduleID;  //needed by [ModuleField]->makeJoinDef()

        $genFile = GENERATED_PATH . "/{$moduleID}/{$moduleID}_RDCUpdate.gen";
        if(file_exists($genFile)){
            include GENERATED_PATH . "/{$moduleID}/{$moduleID}_RDCUpdate.gen"; // returns $RDCinsert, $RDCupdate
        } else {
            print "skipped - no generated file";
            return 'skipped - no generated file';
        }

        //find the last where (strrpos only works on single-character patterns in PHP4)
        $revRDCinsert = strrev($RDCinsert);
        $rPos = strlen($RDCinsert) - strlen('WHERE') - strpos($revRDCinsert, strrev('WHERE'));

        //use the statement until the last WHERE
        $RDCinsert = substr($RDCinsert, 0, $rPos);


        /***************************************** 
            LOOK FOR EXISTING CACHE DEFS
        *****************************************/
        //check if records exist already
        $SQL = "SELECT COUNT(*) FROM `rdc` WHERE ModuleID = '$moduleID'";
        $rdcExists = $mdb2->queryOne($SQL);
        mdb2ErrorCheck($rdcExists);


        /***************************** 
            UPDATE CACHE TABLE
        *****************************/
        $nUpdated = 0;
        if($rdcExists){

            //will fix the generated files to not include the [*updateIDs*] line any more, but this is a workaround for it
            $SQL = str_replace('AND `rdc`.RecordID IN ([*updateIDs*])', '', $RDCupdate);

            //print "s2a-rdc-refresh: created the following UPDATE SQL statement:\n";
            //print "$SQL\n";
            $SQL = TranslateLocalDateSQLFormats($SQL); //will translate to site default formats

            $result = $mdb2->exec($SQL);
            mdb2ErrorCheck($result);
            $nUpdated = $result;

            //print "Successfully updated RecordDescription cache for $nUpdated $moduleID records.\n";
            print "updated $nUpdated records ";
        }


        /************************** 
            INSERT CACHES
        **************************/

        //get the 'live' insert statement from here -- it needs a different WHERE clause
        $SQL = $RDCinsert;
        if($rdcExists){
            $SQL .= "WHERE $moduleID.{$pkField->name} NOT IN (SELECT RecordID FROM `rdc` WHERE ModuleID = '$moduleID')";
        }
        $SQL .= ";";

        //print "s2a-rdc-refresh: created the following INSERT SQL statement:\n";
        //print "$SQL\n";

        $result = $mdb2->exec($SQL);
        mdb2ErrorCheck($result);

        $nInserted = $result;

        //print "Successfully inserted $nInserted RecordDescription cache records for $moduleID.\n";
        print "inserted $nInserted records ";

        if(strlen($nUpdated) == 0){
            $updateMsg = ', no rows in source module';
        } else {
            $updateMsg = ", updated $nUpdated rows";
        }
        return "successful:    inserted $nInserted rows$updateMsg";
    } else {

        print "s2a-rdc-refresh: Module $moduleID has no RecordDescription field\n";
        return 'skipped - no RD field';
    }
}
?>