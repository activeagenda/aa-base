<?php
/**
 * Utility to generate an installation package for distribution.
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

$source_folder = $_SERVER[argv][1];
$assembly_folder = $_SERVER[argv][2];

//handy way to execute this:
//cd to the s2a folder of the working copy
//php s2a-assemble-package.php . active_agenda-0.8b

$debug_prefix = 's2a-assemble:';

//include config files
include_once $source_folder . '/active_agenda/config.php';
include_once $source_folder . '/active_agenda/gen-config.php';
include_once INCLUDE_PATH . '/parse_util.php';

//ensures the AA PEAR library is in the include path
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

//make new directory to assemble package in
if(file_exists($assembly_folder)){
    die("$debug_prefix Folder $assembly_folder (specified as the package assembly folder) exists already.\n");
}
if(!mkdir($assembly_folder)){
    die("$debug_prefix Could not create folder $assembly_folder.\n");
}


print "$debug_prefix exporting code from working copy\n";

//get the latest source
shellCommand("svn export $source_folder $assembly_folder/s2a");

print "$debug_prefix Copying files from .generated folder...\n";

$command = 'cp -Rf '.GENERATED_PATH.'/* '.$assembly_folder.'/s2a/active_agenda/.generated/';
shellCommand($command);
print "\n";
print "$debug_prefix Finished copying files from .generated folder.\n";


//make the uploads folder
$command = 'mkdir '.$assembly_folder.'/s2a/active_agenda/uploads';
shellCommand($command);

//ensure the zip command includes the uploads folder
$command = 'touch '.$assembly_folder.'/s2a/active_agenda/uploads/empty-file.txt';
shellCommand($command);

//ensure the zip command includes the s2alog folder
$command = 'touch '.$assembly_folder.'/s2a/active_agenda/s2alog/errors.log';
shellCommand($command);

//remove non-dist folder
$command = 'rm -Rf '.$assembly_folder.'/s2a/non-dist';
shellCommand($command);

//change file permissions on s2a
shellCommand("chmod -R 755 $assembly_folder/s2a");


//build the file checksum list
print "$debug_prefix Building file checksum list.\n";
$files = getFileList($assembly_folder.'/');
$files = array_merge($files, getFileList($assembly_folder . '/s2a/active_agenda/.generated/'));

$checksum_content = "Active Agenda, release $assembly_folder\r\n\r\n";
foreach($files as $file_path_name){
    $short_path = str_replace($assembly_folder.'/s2a/', '', $file_path_name);
    $file_info = $short_path . "\t" . filesize($file_path_name) . "\t" . md5_file($file_path_name) . "\r\n";
    $checksum_content .= $file_info;
}

//save the checksum file
$checksum_filename = $assembly_folder.'/s2a/'.$assembly_folder.'_checksums.txt';

if($fp = fopen($checksum_filename, 'w')) {

    if(fwrite($fp, $checksum_content)){
        //print no output about saving to log
    } else {
        die( "$debug_prefix could not save to file $checksum_filename. Please check file/folder permissions.\n" );
    }
    fclose($fp);
} else {
    die( "$debug_prefix could not open file $checksum_filename. Please check file/folder permissions.\n" );
}
print "$debug_prefix Finished building file checksum list.\n";

require_once PEAR_PATH . '/DB.php' ;  //PEAR DB class

//connect to DB - we might not need MySQL root privilieges?
global $dbh;
$dbh = DB::connect(GEN_DB_DSN); //gen user privileges
dbErrorCheck($dbh);

//get list of installed modules
$sql = "SELECT ModuleID FROM `mod` WHERE _Deleted = 0 ORDER BY ModuleID";
$modules = $dbh->getCol($sql);

//make a sql subfolder
$sql_folder = $assembly_folder . '/s2a/install';

//for each module, call ModuleInfo to find out master data status
$master_modules = array();
$sample_modules = array();
$skip_modules = array('usr', 'usrdi', 'usrds', 'usrl', 'usrp', 'usrpo', 'usrsd', 'modgt', 'att');
foreach($modules as $module_id){
    $module_info = GetModuleInfo($module_id);
    $is_master_data = $module_info->getProperty('isMasterData');
    if($is_master_data){
        $master_modules[] = $module_id;
    } else {
        if(!in_array($module_id, $skip_modules)){
            $sample_modules[] = $module_id;
        }
    }
}

//put together database SQL file and master temp tables
$sql_table_defs = '';
$mastertemp_table_defs = '';
foreach($modules as $module_id){
    $sql_table_def_file = GENERATED_PATH . '/'. $module_id . '/'. $module_id .'_CreateTables.sql';
    if(file_exists($sql_table_def_file)){
        $table_def_sql = file_get_contents($sql_table_def_file);
        $sql_table_defs .= $table_def_sql."\r\n";

        if(in_array($module_id, $master_modules)){
            $SQLs = explode("-- statement separator --", $table_def_sql);
            $SQL = str_replace("`$master_module_id`", "`{$master_module_id}_tmp`", $SQLs[0]);
            $SQL = trim($SQL);
            $mastertemp_table_defs .= str_replace("`$module_id`", "`{$module_id}_tmp`", $SQL."\r\n-- statement separator --\r\n");
        }
    } else {
        print "$debug_prefix Warning: SQL Table Definition file '$sql_table_def_file' not found.\n";
    }
}


$db_file = $sql_folder.'/empty.sql';
$mastertemp_db_file = $sql_folder.'/mastertemp_structure.sql';

if($fp = fopen($db_file, 'w')) {

    if(fwrite($fp, $sql_table_defs)){
        if($mfp = fopen($mastertemp_db_file, 'w')) {
            if(fwrite($mfp, $mastertemp_table_defs)){
                //successful, no need to report anything
            } else {
                die( "$debug_prefix could not save to file $mastertemp_db_file. Please check file/folder permissions.\n" );
            }
            fclose($mfp);
        } else {
            die( "$debug_prefix could not open file $mastertemp_db_file. Please check file/folder permissions.\n" );
        }
    } else {
        die( "$debug_prefix could not save to file $db_file. Please check file/folder permissions.\n" );
    }
    fclose($fp);
} else {
    die( "$debug_prefix could not open file $db_file. Please check file/folder permissions.\n" );
}

$n_modules = count($modules);
print "$debug_prefix generated empty table definitions for $n_modules modules.\n";


//also don't export the _l tables or deleted records
foreach($modules as $module_id){
    $sql = "TRUNCATE TABLE `{$module_id}_l`";
    $r = $dbh->query($sql);
    dbErrorCheck($r);

    $sql = "DELETE FROM `$module_id` WHERE _Deleted = 1";
    $r = $dbh->query($sql);
    dbErrorCheck($r);
}

print "$debug_prefix Exporting the master data.\n";
$master_file_name = $sql_folder . '/master.sql';

$str_master_tables = join(' ', $master_modules);
$dump_command = 'mysqldump --no-create-info -u ' . GEN_DB_USER . ' -p' . GEN_DB_PASS . ' ' . DB_NAME . ' ' . $str_master_tables . ' > ' . $master_file_name . "\n";

shellCommand($dump_command);

SaveReplacedFields($master_modules, $master_file_name);

print "$debug_prefix Exporting the sample data.\n";
$sample_file_name = $sql_folder . '/sample.sql';

$str_sample_tables = join(' ', $sample_modules);
$dump_command = 'mysqldump --no-create-info -u ' . GEN_DB_USER . ' -p' . GEN_DB_PASS . ' ' . DB_NAME . ' ' . $str_sample_tables . ' > ' . $sample_file_name. "\n";

shellCommand($dump_command);

SaveReplacedFields($sample_modules, $sample_file_name);


print "$debug_prefix Preparing the master upgrade file.\n";
$mastertemp_finds = array();
$mastertemp_replacements = array();
foreach($master_modules as $master_module_id){
    $mastertemp_finds[] = "INSERT INTO `$master_module_id`";
    $mastertemp_finds[] = "LOCK TABLES `$master_module_id`";
    $mastertemp_finds[] = "ALTER TABLE `$master_module_id`";
    $mastertemp_replacements[] = "INSERT INTO `{$master_module_id}_tmp`";
    $mastertemp_replacements[] = "\r\n-- statement separator --\r\nLOCK TABLES `{$master_module_id}_tmp`";
    $mastertemp_replacements[] = "ALTER TABLE `{$master_module_id}_tmp`";
}
$master_file_contents = file_get_contents($master_file_name);
$master_file_contents = str_replace($mastertemp_finds, $mastertemp_replacements, $master_file_contents);
$master_fh = fopen($sql_folder . '/mastertemp_data.sql', 'w');
if(fwrite($master_fh, $master_file_contents)){
    print "$debug_prefix Master upgrade data file saved correctly.\n";
} else {
    die( "$debug_prefix Could not save master upgrade data file.\n");
}
fclose($master_fh);


$assembly_folder_parts = explode('/', $assembly_folder);
$archive_name = end($assembly_folder_parts);

$archive_file_name = $archive_name . '.tar.bz2';
$command = "tar cjf $archive_file_name $archive_name";
shellCommand($command);

$command = "md5sum $archive_file_name > $archive_file_name.md5";
shellCommand($command);

//TODO: avoid using -l on binary files
$archive_file_name = $archive_name . '.zip';
//$command = "zip -Dlr $archive_file_name $archive_name";
$command = "zip -Dr $archive_file_name $archive_name";
shellCommand($command);


$command = "md5sum $archive_file_name > $archive_file_name.md5";
shellCommand($command);

print "$debug_prefix All done!\n";


function SaveReplacedFields($modules, $data_file_name)
{
    print "Inserting field name lists in $data_file_name\n";
    global $dbh;
    $module_inserts = array();

    //get the current field order for each master table
    foreach($modules as $module_id){
        $sql = "describe `$module_id`";
        $table_info = $dbh->getAll($sql);
        $table_fields = array();
        foreach($table_info as $table_info_row){
            $table_fields[] = $table_info_row[0]; //copy field names in correct order...
        }

        $module_inserts["INSERT INTO `$module_id` VALUES"] = "INSERT INTO `$module_id` (" . join(', ',$table_fields). ') VALUES';
    }

    $table_finds = array_keys($module_inserts);

    $sql_data_genfile = fopen($data_file_name, 'r');
    $sql_data_fixedfile = fopen($data_file_name .'.fixed', 'w');
    if ($sql_data_genfile) {
        while (!feof($sql_data_genfile)) {
            $line = fgets($sql_data_genfile, 4096);
            $fixed_line = str_replace($table_finds, $module_inserts, $line);

            if(!feof($sql_data_genfile)){
                if(fwrite($sql_data_fixedfile, $fixed_line)){
                    //working fine
                } else {
                    die( "$debug_prefix could not save to file $data_file_name. Please check file/folder permissions.\n" );
                }
            }
        }
        fclose($sql_data_genfile);
        fclose($sql_data_fixedfile);
    }

    print "Finished inserting field name lists in {$data_file_name}.fixed\n";
    unlink($data_file_name);

    rename($data_file_name . '.fixed', $data_file_name);
    print "Renamed file back to {$data_file_name}\n";

}


function getFileList($location){
    $file_list = glob($location . '*', GLOB_BRACE);
    $dir_list = array();
    $files = array();
    foreach($file_list as $file_path_name){
        if(is_file($file_path_name)){
            $files[] = $file_path_name;
        }
        if(is_dir($file_path_name)){
            $dir_list[] = $file_path_name;
        }
    }
    if(count($dir_list) > 0){
        foreach($dir_list as $dir_path_name){
            //print "dir: $dir_path_name\n";
            $files = array_merge($files, getFileList($dir_path_name . '/'));
        }
    }
    return $files;
}

?>
