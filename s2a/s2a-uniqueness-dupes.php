<?php
/**
 * Generates a report on duplicates in uniqueness indexes.
 *
 * This script checks for duplicates in each module that has at least one defined
 * uniqueness index, and prints its findings. Optionally, these findings can be
 * written to a file.
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
 * @version        SVN: $Revision: 1540 $
 * @last-modified  SVN: $Date: 2009-02-28 14:04:13 -0800 (Sat, 28 Feb 2009) $
 */


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

$config['match'] =
    array('short'   => 'm',
        'min'     => 0,
        'max'     => 1,
        'desc'    => 'A wildcard expression that matches the IDs of the modules to generate. Use % as wildcard character. Examples: %, ac%, act',
        'default' => '%'
    );
$config['outfile'] =
    array('short'   => 'f',
        'min'     => 0,
        'max'     => 1,
        'desc'    => 'Whether to write the reported uniqueness problems to a text file. You may supply a file name as argument.',
        'default' => 'uniqueness-dupes.txt'
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

//getting the passed parameters
$Project                = 'active_agenda';
$ModuleMatch            = $args->getValue('match');
$Outfile                = $args->getValue('outfile');

if(empty($ModuleMatch)){
    $ModuleMatch = '%';
}

print "s2a-uniqueness-dupes: module match = $ModuleMatch\n";

//assumes we're in the 's2a' folder 
$site_folder = realpath(dirname(__FILE__).'');
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

//this include contains utility functions
include_once INCLUDE_PATH . '/parse_util.php';


/**
 * Sets custom error handler
 */
set_error_handler('handleError');


/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
define('EXEC_STATE', 2);

if(!empty($Outfile)){
    print "Outfile: $Outfile\n";
}

$moduleMatchPattern = str_replace('%', '*', $ModuleMatch);

print "Checking generated ModuleInfo files for uniqueness definitions:\n";
$filePattern = GENERATED_PATH."/$moduleMatchPattern/{$moduleMatchPattern}_ModuleInfo.gen";
$foundUniquenessIndexes = array();
$moduleInfos = glob($filePattern);

global $SQLBaseModuleID;
$mdb2 =& GetMDB2();

$foundDupes = array();

foreach($moduleInfos as $file_path){
    $uniquenessIndexes = null; //re-initialize
    $primaryKeys = null;
    include $file_path; //sets a couple variables, we're looking for $uniquenessIndexes and $primaryKeys
    $fileName = basename($file_path);
    list($moduleID, ) = explode('_', $fileName);

    if(empty($uniquenessIndexes)){
        print ".";
    } else {
        print "x";

        $SQLBaseModuleID = $moduleID;
        //print "\n$moduleID\n";
        //trace($uniquenessIndexes);

        $pkFieldName = end($primaryKeys);
        foreach($uniquenessIndexes as $index => $indexInfo){
            $sql = "SELECT $pkFieldName, '$index' AS IndexName FROM `{$moduleID}` ";
            $joins = array();
            $indexSelects = '';
            $groupBys = array();
            foreach($indexInfo as $fieldName => $fieldInfo){
                $indexSelects .= ", {$fieldInfo['s']} AS $fieldName";
                $groupBys[] = $fieldInfo['s'];
                if(isset($fieldInfo['j'])){
                    $joins = array_merge($joins, $fieldInfo['j']);
                }
            }

            $sql = "SELECT count($pkFieldName) AS c, '$index' AS IndexName$indexSelects FROM `{$moduleID}` ";
            if(count($joins) > 0){
                $joins = SortJoins($joins);
                $sql .= join("\n", $joins);
            }
            $sql .= " WHERE `{$moduleID}`._Deleted = 0";
            $sql .= ' GROUP BY '.join(',', $groupBys);
            $sql .= ' HAVING c > 1';
            //trace($sql);

            $r = $mdb2->queryAll($sql);
            mdb2ErrorCheck($r);

            if(count($r) > 0){
                $foundDupes[$moduleID] = $r;
            }
        }
    }
}
print "\n\n";
if(0 == count($foundDupes)){
    print "No duplicate records found. All is well.\n";
    exit;
}
//trace($foundDupes);

$messages = '';
foreach($foundDupes as $moduleID => $rows){
    $message = "Module `$moduleID`:\n";
    print $message;
    $messages .= $message;
    foreach($rows as $row){
        $indexName = $row['IndexName'];
        $count = $row['c'];
        unset($row['IndexName']);
        unset($row['c']);

        $message = "Found $count records where ";
        print $message;
        $messages .= $message;
        $valueStrings = array();
        foreach($row as $field => $value){
            $valueStrings[] = "$field = '$value'";
        }
        $message = join(' and ', $valueStrings)." (uix: $indexName)\n";
        print $message;
        $messages .= $message;
    }
    print "\n";
    $messages .= "\n";
}

$fileinfo = "Module match pattern: $ModuleMatch\n";
$fileinfo .= "Date/time: ".date('Y-m-d H:i:s')."\n";
$fileinfo .= "\n";
//print $messages;

$messages = $fileinfo . $messages;

if(!empty($Outfile)){
    $result = file_put_contents($Outfile, $messages);
    if(false === $result){
        print "Could not write file '$Outfile'.\n";
    } else {
        print "Wrote file $Outfile ($result bytes).\n";
    }
}

//end file s2a-uniqueness-dupes.php