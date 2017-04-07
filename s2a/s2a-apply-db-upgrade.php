<?php
/**
 * Utility to upgrade Active Agenda database and data.
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



define('EXEC_STATE', 2);


$project = $_SERVER[argv][1];  //folder location
if(empty($project)){
   $project = 'active_agenda';
}

//assumes we're in the 's2a' folder 
$site_folder = realpath(dirname($_SERVER['SCRIPT_FILENAME']).'');
$site_folder .= '/'.$project;

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
require_once PEAR_PATH . '/DB.php' ;  //PEAR DB class
include_once INCLUDE_PATH . '/general_util.php';
include_once INCLUDE_PATH . '/parse_util.php';
include_once INCLUDE_PATH . '/web_util.php'; //need dbQuote()
include_once CLASSES_PATH . '/module.class.php';

$debug_prefix = 's2a-upgrade-db:';

$upgrade_filepaths = glob(S2A_FOLDER.'/install/db_upgrade*.gen');
if(count($upgrade_filepaths) == 0){
    die("Could not find any upgrade files");
}

if(count($upgrade_filepaths) == 1){
    $choice = 0;
} else {
    $choice = ChooseUpgrade($upgrade_filepaths);
}
$upgrade_filepath = $upgrade_filepaths[$choice];
print "Getting upgrade file from $upgrade_filepath.\n";
include($upgrade_filepath);


$dbFormat = new DBFormat($targetDB);  //object that provides db-specific data type changes
$dataTypes = $dbFormat->dataTypes;

//connect with superuser privileges - regular user has no permission to
//change table structure
global $dbh;
$dbh = DB::connect(GEN_DB_DSN);

dbErrorCheck($dbh);

//hack for modch:
$SQL = "TRUNCATE TABLE `modch`;";
print "Removing data from Module Charts table (modch)\n";
print $SQL ."\n";
$r = $dbh->query($SQL);
dbErrorCheck($r, false);

if(count($module_changes['remove_modules']) > 0){
    foreach($module_changes['remove_modules'] as $moduleID){
        $SQL = "DROP TABLE `$moduleID`;";
        print "Dropping table `$moduleID`\n";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r, false);

        $SQL = "DROP TABLE `{$moduleID}_l`;";
        print "Dropping table `{$moduleID}_l`\n";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r, false);

        print "Removing `$table_name` references from global modules\n";
        $SQL = "DELETE FROM `mod` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `modd` WHERE (ModuleID = '$moduleID') OR (DependencyID = '$moduleID')";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `usrp` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `spt` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `smc` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `rdc` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        $SQL = "DELETE FROM `dsbc` WHERE ModuleID = '$moduleID'";
        print $SQL ."\n";
        $r = $dbh->query($SQL);
        dbErrorCheck($r);

        /*
            We should probably remove associations from global modules and central sub-modules as well.

            This deletion should be used as little as possible, but I'm forced to implement it: A module (filr) was replaced with another, using the same module ID (BAD idea!), and this is the only way to allow the upgrade to install the "new" filr...

            /MJT
        */
    }
}

//add new modules
if(count($module_changes['new_modules']) > 0){
    foreach($module_changes['new_modules'] as $new_module_id){

        $sql_file_path = GENERATED_PATH . "/{$new_module_id}/{$new_module_id}_CreateTables.sql";
        if(!file_exists($sql_file_path)){
            die("Could not find $sql_file_path\n");
        }
        $SQL = file_get_contents($sql_file_path);

        $SQLs = explode("-- statement separator --", $SQL);

        foreach($SQLs as $SQL){
            $SQL = trim($SQL);
            if(!empty($SQL)){
                $table_name = substr($SQL, strpos($SQL, '`')+1, 9);
                $table_name = substr($table_name, 0, strpos($table_name, '`', 1));

                //check if table exists
                if(TableExists($table_name)){
                    print "Table `$table_name` exists already, skipping.\n";
                } else {
                    print "Adding table `$table_name`\n";
                    print $SQL;
                    $r = $dbh->query($SQL);
                    dbErrorCheck($r);
                }
            }
        }
    }
}


if(count($module_changes['add']) > 0){
    foreach($module_changes['add'] as $moduleID => $fields){
        print "Adding fields for module $moduleID.\n";
        AddFields($moduleID, $fields, false); //main table
        AddFields($moduleID, $fields, true);  //log table
    }
}


if(count($module_changes['remote_new']) > 0){
    print "Handling new remotefields for module $moduleID.\n";

    foreach($module_changes['remote_new'] as $moduleID => $fields){
        do {
            //check table structure to see if the new structure is applied already:
            $SQL = "SHOW COLUMNS FROM `$moduleID`;";
            $columns = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
            dbErrorCheck($columns);

            $existingColumns = array();
            foreach($columns as $column_info){
                $existingColumns[] = $column_info['Field'];
            }

            $dh = GetDataHandler($moduleID); //must get NEW dh
            $mi = GetModuleInfo($moduleID);

            $recordIDField = $mi->getPKField();

            $SQL = "SELECT \n$recordIDField";
            foreach($fields as $old_name => $new_name){
                if(!in_array($old_name, $existingColumns)){
                    if(prompt("It appears that the $moduleID module has been modified or changed already. Therefore, this program cannot migrate data into the new remotefield(s). Presumably, this has already been done. Answer 'y' to skip the $moduleID module (recommended).")){
                        break 2;
                    }
                }
                $SQL .= ",\n$old_name AS $new_name";
            }
            $SQL .= "\nFROM `$moduleID`\nWHERE _Deleted = 0";
    
            $r = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
            dbErrorCheck($r);
            if(count($r) > 0){
                foreach($r as $row){
                    print "Applying remote fields for: \n";
                    $recordID = $row[$recordIDField];
                    unset($row[$recordIDField]);
                    $cleanRow = array();
                    foreach($row as $fieldname => $fieldvalue){
                        $cleanRow[$fieldname] = addslashes($fieldvalue);
                    }
                    $dh->saveRow($cleanRow, $recordID);
                }
            }
        } while(false);
    }
}


if(count($module_changes['rename']) > 0){
    foreach($module_changes['rename'] as $moduleID => $fields){
        print "Renaming fields for module $moduleID.\n";
        RenameFields($moduleID, $fields, false); //main table
        RenameFields($moduleID, $fields, true);  //log table
    }
}


if(count($module_changes['alter']) > 0){
    foreach($module_changes['alter'] as $moduleID => $fields){
        print "Altering fields for module $moduleID.\n";
        AlterFields($moduleID, $fields, false); //main table
        AlterFields($moduleID, $fields, true);  //log table
    }
}


//Field changed from remotefield to tablefield


if(count($module_changes['drop']) > 0){
    foreach($module_changes['drop'] as $moduleID => $fields){
        print "Dropping fields for module $moduleID.\n";
        DropFields($moduleID, $fields, false); //main table
        DropFields($moduleID, $fields, true);  //log table
    }
}


if(count($module_changes['master_modules']) > 0){
    if(prompt("Import new master data?")){
        $mastertemp_structure_file = S2A_FOLDER.'/install/mastertemp_structure.sql';
        $mastertemp_data_file = S2A_FOLDER.'/install/mastertemp_data.sql';
        if(!file_exists($mastertemp_structure_file)){
            die("Could not find file $mastertemp_structure_file\n");
        }
        if(!file_exists($mastertemp_data_file)){
            die("Could not find file $mastertemp_data_file\n");
        }

        /**
         *  Attempt to find mysql.exe on Windows, because it's not in the PATH by default
         */
        if(isWindows()){
            if(!$mysql_folder = findMySQLexe()){
                print("Could not find the MySQL executable in the expected default location.\n");
                if(prompt("Do you know the location of the MySQL executable? Answering 'n' will exit the program.")){
                    die("Exited the program.\n");
                }
                $mysql_folder = textPrompt("Please enter the folder location where the MySQL executable is located.");
            }
            $mysql_path = dirname($mysql_folder);

            $path_val = getenv('PATH');
            if(putenv("PATH=$path_val;$mysql_path")){
                $mysql_command = 'mysql.exe';
            } else {
                die("$debug_prefix could not set PATH\n");
            }
            $path_val = getenv('PATH');
        } else {
            $mysql_command = 'mysql';

            $mysql_loc = shellCommand('which '.$mysql_command, false, false);
            if(empty($mysql_loc)){
                do {
                    $mysql_command = textPrompt("Please enter the location where the MySQL executable is located, uncluding the name of the mysql executable.");
                    print "You entered: $mysql_command\n";
                } while(!prompt("Is this correct?"));
            }
        }

        print "$debug_prefix Installing temporary master tables...\n";
        shellCommand($mysql_command .' -u '.GEN_DB_USER.' -p'.GEN_DB_PASS.' '.DB_NAME.' < "'.$mastertemp_structure_file.'"');

        print "$debug_prefix Populating temporary master tables...\n";
        shellCommand($mysql_command .' -u '.GEN_DB_USER.' -p'.GEN_DB_PASS.' '.DB_NAME.' < "'.$mastertemp_data_file.'"');

        foreach($module_changes['master_modules'] as $master_module_id){
            $moduleInfo = GetModuleInfo($master_module_id);
            $pk_fields = $moduleInfo->getProperty('primaryKeys');
            $pk_sqls = array();
            foreach($pk_fields as $pk_field){
                $pk_sqls[] = "main.$pk_field = tmp.$pk_field";
            }
            $pk_sql = join(" AND ", $pk_sqls);

            $r = $dbh->query("ALTER TABLE `$master_module_id` DISABLE KEYS");
            dbErrorCheck($r);

            $insert_sql = "INSERT INTO `$master_module_id` SELECT tmp.* FROM `{$master_module_id}_tmp` AS tmp LEFT OUTER JOIN `$master_module_id` AS main ON $pk_sql WHERE main.{$pk_fields[0]} IS NULL";
            print "executing $insert_sql\n";
            $r = $dbh->query($insert_sql);
            dbErrorCheck($r);

            print "Rows inserted: ".$dbh->affectedRows()."\n";

            $r = $dbh->query("ALTER TABLE `$master_module_id` ENABLE KEYS;");
            dbErrorCheck($r);

            $r = $dbh->query("DROP TABLE `{$master_module_id}_tmp`");
            dbErrorCheck($r);
        }
    }
}

print "Database upgrade was applied successfully!\n";


//functions

function AddFields($moduleID, $fields, $applyToLog)
{
    global $dbh;
    global $dataTypes;
    if($applyToLog){
        $table_name = $moduleID.'_l';
    } else {
        $table_name = $moduleID;
    }

    $db_fields = GetDBFields($table_name);

    $sql_clauses = array();
    foreach($fields as $field_ix => $field_name){
        if(in_array($field_name, $db_fields)){
            print "Field `$table_name`.$field_name exists already, skipping.\n";
            //column added already, so remove it from the $fields array
            unset($fields[$field_ix]);
        } else {
            $modulefield = GetModuleField($moduleID, $field_name);
            //print_r($modulefield);
            $sql_clauses[] = "ADD COLUMN {$modulefield->name} {$dataTypes[$modulefield->dataType]} {$modulefield->dbFlags}";
        }
    }
    if(count($fields) > 0){
        $SQL = "ALTER TABLE `$table_name` \n";
        $SQL .= join(",\n", $sql_clauses);

        print "Applying table changes:\n";
        print $SQL."\n\n";

        $r = $dbh->query($SQL);
        dbErrorCheck($r);
    } else {
        print "Changes to `$table_name` already applied, skipping.\n";
    }
    print "\n";
}


function RenameFields($moduleID, $fields, $applyToLog)
{
    global $dbh;
    global $dataTypes;
    if($applyToLog){
        $table_name = $moduleID.'_l';
    } else {
        $table_name = $moduleID;
    }

    $db_fields = GetDBFields($table_name);

    $sql_clauses = array();
    foreach($fields as $old_name => $field_name){
        if(in_array($field_name, $db_fields)){
            print "Field `$table_name`.$field_name exists already, skipping.\n";
            //column with new name exists already, so remove it from the $fields array
            unset($fields[$old_name]);
        } else {
            $modulefield = GetModuleField($moduleID, $field_name);
            //print_r($modulefield);
            $sql_clauses[] = "CHANGE COLUMN $old_name {$modulefield->name} {$dataTypes[$modulefield->dataType]} {$modulefield->dbFlags}";
        }
    }

    if(count($fields) > 0){
        $SQL = "ALTER TABLE `$table_name` \n";
        $SQL .= join(",\n", $sql_clauses);

        print "Applying table changes:\n";
        print $SQL."\n\n";

        $r = $dbh->query($SQL);
        dbErrorCheck($r);
    } else {
        print "Changes to `$table_name` already applied, skipping.\n";
    }
    print "\n";
}


function AlterFields($moduleID, $fields, $applyToLog)
{
    global $dbh;
    global $dataTypes;
    if($applyToLog){
        $table_name = $moduleID.'_l';
    } else {
        $table_name = $moduleID;
    }

    $db_fields = GetDBFields($table_name);

    $sql_clauses = array();
    foreach($fields as $field_ix => $field_name){
        if(!in_array($field_name, $db_fields)){
            if(!prompt("WARNING: column $moduleID.$field_name does not exist. Continue?")){
                die("exit\n");
            }
        } else {
            $modulefield = GetModuleField($moduleID, $field_name);
            //print_r($modulefield);
            $sql_clauses[] = "MODIFY COLUMN {$modulefield->name} {$dataTypes[$modulefield->dataType]} {$modulefield->dbFlags}";
        }
    }

    if(count($fields) > 0){
        $SQL = "ALTER TABLE `$table_name` \n";
        $SQL .= join(",\n", $sql_clauses);

        print "Applying table changes:\n";
        print $SQL."\n\n";

        $r = $dbh->query($SQL);
        dbErrorCheck($r);
    } else {
        print "WARNING: Nothing to change in `$table_name`, skipping.\n";
    }
    print "\n";
}


function DropFields($moduleID, $fields, $applyToLog)
{
    global $dbh;
    if($applyToLog){
        $table_name = $moduleID.'_l';
    } else {
        $table_name = $moduleID;
    }

    $db_fields = GetDBFields($table_name);

    $sql_clauses = array();
    foreach($fields as $field_ix => $field_name){
        if(!in_array($field_name, $db_fields)){
            print "Field `$table_name`.$field_name dropped already, skipping.\n";
            //column with new name exists already, so remove it from the $fields array
            unset($fields[$field_ix]);
        } else {
            $sql_clauses[] = "DROP COLUMN $field_name";
        }
    }

    if(count($fields) > 0){
        $SQL = "ALTER TABLE `$table_name` \n";
        $SQL .= join(",\n", $sql_clauses);

        print "Applying table changes:\n";
        print $SQL."\n\n";

        $r = $dbh->query($SQL);
        dbErrorCheck($r);
    } else {
        print "Changes to `$table_name` already applied, skipping.\n";
    }
    print "\n";
}


function GetDBFields($table_name)
{
    global $dbh;

    $SQL = "DESCRIBE `$table_name`";
    $describe_result = $dbh->getAll($SQL, DB_FETCHMODE_ASSOC);
    dbErrorCheck($describe_result);
    $db_fields = array();

    foreach($describe_result as $describe_row){
        $db_fields[] = $describe_row['Field'];
    }

    return $db_fields;
}


function ChooseUpgrade($upgrade_filepaths)
{
    print "Available upgrades:\n";
    foreach($upgrade_filepaths as $ix=>$upgrade_filepath){
        $upgrade_name = basename($upgrade_filepath);
        print "$ix: $upgrade_name\n";
    }
    $max_ix = end(array_keys($upgrade_filepaths));
    $choice = textPrompt("Enter the number (0 - $max_ix) next to the upgrade you would like to apply:");
    $choice = intval($choice);
    if(0 <= $choice && $choice <= $max_ix){
        print "You chose ".basename($upgrade_filepaths[$choice]).".\n";
        if(!prompt("Is this the upgrade you would like to apply?")){
            $choice = ChooseUpgrade($upgrade_filepaths);
        }
    } else {
        print "Your response must be a number between 0 and $max_ix.\n";
        if(prompt("Try again?")){
            $choice = ChooseUpgrade($upgrade_filepaths);
        } else {
            die("Exit\n");
        }
    }
    return $choice;
}


function TableExists($table_name)
{
    $SQL = "SHOW TABLES LIKE '$table_name';";
    global $dbh;
    $r = $dbh->getCol($SQL);
    dbErrorCheck($r);

    if(in_array($table_name, $r)){
        return true;
    } else {
        return false;
    }
}
?>