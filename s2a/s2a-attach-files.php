<?php
/**
 * Utility to attach files to records in an AA database. The files must reside in a
 * (temporary) directory which must also contain a description file named
 * attachment_info.csv.
 * 
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
 * @last-modified  SVN: $Date: 2009-01-26 22:56:18 -0800 (Mon, 26 Jan 2009) $
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

$config['project'] =
    array('short' => 'p',
        'min'   => 0,
        'max'   => 1,
        'desc'  => 'The s2a project name. Must be a folder under the s2a folder.',
        'default' => 'active_agenda'
    );
$config['directory'] =
    array('short'   => 'd',
        'min'     => 1,
        'max'     => 1,
        'desc'    => 'The name of a directory that contains files to be attached. (required)'
    );
$config['purge-dupes'] =
    array('short'   => 'pd',
        'min'     => 0,
        'max'     => 0,
        'desc'    => 'Whether to remove any duplicate attachments (caused by previous imports). This removed exact duplicated only (same related module record, file name, file size).',
        'default' => false
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
$Project   = $args->getValue('project');
$Directory = $args->getValue('directory');
$Directory = trim($Directory);
$PurgeDupes = $args->getValue('purge-dupes');

if(empty($Directory)){
    echo Console_Getargs::getHelp($config)."\n";
    echo "\nYou need to specify a directory containing files to be attached.\n\n";
    exit;
}

if(empty($Project)){
    $Project = 'active_agenda';
}

print "s2a-attach-files: project = $Project, file directory = $Directory\n";

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

set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//library includes
include_once INCLUDE_PATH . '/parse_util.php';
include_once CLASSES_PATH . '/modulefields.php';
include_once CLASSES_PATH . '/data_handler.class.php';
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/web_util.php';


/**
 * Sets custom error handler
 */
set_error_handler('handleError');


/**
 * Defines execution state as 'non-generating command line'.  Several classes and
 * functions behave differently because of this flag.
 */
DEFINE('EXEC_STATE', 2);

$mdb2 = GetMDB2();


//check that the input file exists
if(!file_exists($Directory)){
    die("Could not find input directory $Directory.\n");
}

$DataFile = $Directory . '/attachment_info.csv';
$LogFile  = $Directory . '/attachment_log.csv';

//check that the input file exists
if(!file_exists($DataFile)){
    die("Could not find attachemnt data file $DataFile.\n");
}

print "Reading CSV file.\n";

$expected_headers = array('RelatedModuleID', 'RelatedRecordID', 'FileName');

$headers = array();
$rows = array();

$row_ix = 0;
$handle = fopen($DataFile, "r");
while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    $row_ix++;
    if(1 == $row_ix){
        $nFields = count($data);
        foreach($data as $header){
            $headers[] = trim($header);
        }

        foreach($expected_headers as $expected_header){
            if(!in_array($expected_header, $headers)){
                die("Expected field $expected_header in the info file was not found.\n");
            }
        }
    } else {
        if(count($data) != $nFields){
            print "WARNING: could not read line $row_ix: not the same number of fields as in the header.\n";
        } else {
            foreach($data as $field_ix => $data_cell){
                $rows[$row_ix][$headers[$field_ix]] = trim($data_cell);
            }
        }
    }
}
fclose($handle);

//verify consistency between available files and info file
$missing_files = array();
$matched_files = array();
foreach($rows as $row_ix => $row){
    //verify file names
    if(file_exists($Directory.'/'.$row['FileName'])){
        $matched_files[] = trim($row['FileName']);
    } else {
        $missing_files[] = trim($row['FileName']);
        unset($rows[$row_ix]);
    }
}

$extra_files = array();
$file_list = glob($Directory . '/*.*');
foreach($file_list as $file_path){
    if($DataFile == $file_path){
        continue;
    }
    if($LogFile == $file_path){
        continue;
    }
    $file_name = basename($file_path);
    if(!in_array($file_name, $matched_files)){
        $extra_files[] = $file_name;
    }
}

$confirm = false;
if(count($extra_files) > 0){
    print "\nWARNING: Some files were not described in the info file. They will be skipped:\n";
    print join(', ', $extra_files)."\n";
    $confirm = true;
}
if(count($missing_files) > 0){
    print "\nWARNING: Some files are missing from directory $Directory:\n";
    print join(', ', $missing_files)."\n";
    $confirm = true;
}
if($confirm && !prompt("\nContinue attaching files?")){
    print "Aborting\n";
    exit;
}

$dataHandler = GetDataHandler('att');
$dataHandler->startTransaction();

$logFile = fopen($LogFile, 'w');
$logHeaders = $headers;
if(!in_array('Description', $logHeaders)){
    $logHeaders[] = 'Description';
}
$logHeaders = array_merge($logHeaders, array('FileSize', 'CopiedTo'));
fputcsv($logFile, $logHeaders);

foreach($rows as $row){
    $filePath = $Directory.'/'.$row['FileName'];

    if(empty($row['Description'])){
        $row['Description'] = $row['FileName'];
    }

    $row['FileSize'] = filesize($filePath);

    if($PurgeDupes){
        $SQL = "SELECT AttachmentID FROM `att` WHERE _Deleted = 0 AND RelatedModuleID = '{$row['RelatedModuleID']}' AND RelatedRecordID = '{$row['RelatedRecordID']}' AND FileName = '{$row['FileName']}' AND FileSize = {$row['FileSize']}";

        $existingAttachmentIDs = $mdb2->queryCol($SQL);
        mdb2ErrorCheck($existingAttachmentIDs);

        if(count($existingAttachmentIDs) > 0){
            trace("Purging dupes");
            $attachmentIDs = array();
            foreach($existingAttachmentIDs as $existingAttachmentID){
                $esistingFilePath = UPLOAD_PATH . "/{$row['RelatedModuleID']}/att_{$row['RelatedModuleID']}_{$row['RelatedRecordID']}_{$existingAttachmentID}.dat";
                if(file_exists($esistingFilePath)){
                    if(!unlink($esistingFilePath)){
                        trigger_error("Could not delete attached file $esistingFilePath.", E_USER_WARNING);
                    }
                    trace("Deleted: $esistingFilePath");
                } else {
                    trace("No file to delete: $esistingFilePath");
                }
            }
            $SQL = "UPDATE `att` SET _Deleted = 1 WHERE AttachmentID IN (".join(',',$existingAttachmentIDs).")";
            $r = $mdb2->exec($SQL);
            mdb2ErrorCheck($r);
        }
    }

    $attachmentID = $dataHandler->importRow($row);

    //build the file name
    $destination = UPLOAD_PATH . "/{$row['RelatedModuleID']}/att_{$row['RelatedModuleID']}_{$row['RelatedRecordID']}_{$attachmentID}.dat";

    //create the folder if needed
    if(!file_exists(dirname($destination))){
        mkdir(dirname($destination));
    }

    if(copy($filePath, $destination)){
        $row['CopiedTo'] = $destination;
        fputcsv($logFile, $row);

    } else {
        die("Could not copy file $filePath, aborting.\n");
    }
}
$dataHandler->endTransaction();
fclose($logFile);
